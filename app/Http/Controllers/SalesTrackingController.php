<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SalesTrackingController extends Controller
{
    public const STAGES = [
        'new' => 'Nuevo',
        'contacted' => 'Contactado',
        'demo_scheduled' => 'Demostración programada',
        'needs_review' => 'Evaluando necesidades',
        'proposal_prepared' => 'Propuesta preparada',
        'proposal_sent' => 'Propuesta enviada',
        'negotiating' => 'En negociación',
        'won' => 'Acuerdo cerrado',
        'lost' => 'No concretado',
        'closed' => 'Cerrado (estado anterior)',
    ];

    public const PROPOSAL_STATUSES = [
        'draft' => 'Borrador',
        'sent' => 'Enviada',
        'accepted' => 'Aceptada',
        'declined' => 'No aceptada',
    ];

    private const ACTIVITY_TYPES = [
        'call' => 'Llamada',
        'email' => 'Correo',
        'meeting' => 'Reunión',
        'demo' => 'Demostración',
        'note' => 'Nota',
    ];

    public function show(Request $request, string $type, int $id): View
    {
        $this->authorizeOperator($request);
        [$table, $subjectType] = $this->subject($type);
        $record = DB::table($table)->where('id', $id)->first();
        abort_unless($record, 404);

        return view('panel.sales-tracking', [
            'type' => $type,
            'record' => $record,
            'companyName' => $record->company_name,
            'contactName' => $type === 'contacto'
                ? $record->contact_name
                : trim($record->first_name.' '.$record->last_name),
            'stage' => $type === 'contacto' ? $record->status : $record->sales_stage,
            'stages' => self::STAGES,
            'activityTypes' => self::ACTIVITY_TYPES,
            'proposalStatuses' => self::PROPOSAL_STATUSES,
            'operators' => DB::table('users')->where('is_minka_operator', true)->orderBy('name')->get(['id', 'name']),
            'activities' => DB::table('commercial_activities')
                ->leftJoin('users', 'users.id', '=', 'commercial_activities.operator_id')
                ->where('subject_type', $subjectType)->where('subject_id', $id)
                ->select('commercial_activities.*', 'users.name as operator_name')
                ->orderByDesc('occurred_at')->orderByDesc('commercial_activities.id')->get(),
            'proposals' => DB::table('commercial_proposals')
                ->leftJoin('users', 'users.id', '=', 'commercial_proposals.operator_id')
                ->where('subject_type', $subjectType)->where('subject_id', $id)
                ->select('commercial_proposals.*', 'users.name as operator_name')
                ->orderByDesc('commercial_proposals.id')->get(),
            'backUrl' => $type === 'solicitud'
                ? route('minka.application', $id)
                : route('minka.commercial'),
        ]);
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $this->authorizeOperator($request);
        [$table, $subjectType] = $this->subject($type);
        $data = $request->validate([
            'stage' => ['required', Rule::in(array_keys(self::STAGES))],
            'sales_owner_id' => ['nullable', Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_minka_operator', true))],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($request, $table, $subjectType, $id, $type, $data): void {
            $record = DB::table($table)->where('id', $id)->lockForUpdate()->first();
            abort_unless($record, 404);
            $stageColumn = $type === 'contacto' ? 'status' : 'sales_stage';
            $nextAt = isset($data['next_follow_up_at'])
                ? Carbon::parse($data['next_follow_up_at'])->format('Y-m-d H:i:s')
                : null;

            DB::table($table)->where('id', $id)->update([
                $stageColumn => $data['stage'],
                'sales_owner_id' => $data['sales_owner_id'] ?? null,
                'next_follow_up_at' => $nextAt,
                'updated_at' => now(),
            ]);

            $changes = [];
            if ($record->{$stageColumn} !== $data['stage']) {
                $changes[] = 'Etapa: '.(self::STAGES[$record->{$stageColumn}] ?? $record->{$stageColumn})
                    .' → '.self::STAGES[$data['stage']];
            }
            if ((string) $record->sales_owner_id !== (string) ($data['sales_owner_id'] ?? null)) {
                $changes[] = 'Se actualizó el responsable comercial.';
            }
            if ($record->next_follow_up_at !== $nextAt) {
                $changes[] = $nextAt ? 'Próximo seguimiento: '.$nextAt : 'Se quitó la fecha de seguimiento.';
            }
            if ($changes !== []) {
                $this->activity($subjectType, $id, (int) $request->user()->id, 'update', implode(' ', $changes));
            }
        });

        return back()->with('panel_success', 'Seguimiento comercial actualizado.');
    }

    public function addActivity(Request $request, string $type, int $id): RedirectResponse
    {
        $this->authorizeOperator($request);
        [$table, $subjectType] = $this->subject($type);
        abort_unless(DB::table($table)->where('id', $id)->exists(), 404);
        $data = $request->validate([
            'activity_type' => ['required', Rule::in(array_keys(self::ACTIVITY_TYPES))],
            'description' => ['required', 'string', 'min:3', 'max:5000'],
            'occurred_at' => ['nullable', 'date', 'before_or_equal:now'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($request, $table, $subjectType, $id, $data): void {
            DB::table('commercial_activities')->insert([
                'subject_type' => $subjectType,
                'subject_id' => $id,
                'operator_id' => $request->user()->id,
                'activity_type' => $data['activity_type'],
                'description' => trim($data['description']),
                'occurred_at' => isset($data['occurred_at']) ? Carbon::parse($data['occurred_at']) : now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if (! empty($data['next_follow_up_at'])) {
                DB::table($table)->where('id', $id)->update([
                    'next_follow_up_at' => Carbon::parse($data['next_follow_up_at']),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('panel_success', 'Actividad registrada.');
    }

    public function addProposal(Request $request, string $type, int $id): RedirectResponse
    {
        $this->authorizeOperator($request);
        [$table, $subjectType] = $this->subject($type);
        abort_unless(DB::table($table)->where('id', $id)->exists(), 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'billing_period' => ['required', Rule::in(['monthly', 'annual', 'one_time'])],
            'modules' => ['nullable', 'string', 'max:5000'],
            'scope' => ['nullable', 'string', 'max:10000'],
            'valid_until' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'sent'])],
            'sent_at' => ['nullable', 'date', 'before_or_equal:now'],
        ]);

        DB::transaction(function () use ($request, $subjectType, $id, $data): void {
            $proposalId = DB::table('commercial_proposals')->insertGetId([
                'subject_type' => $subjectType,
                'subject_id' => $id,
                'operator_id' => $request->user()->id,
                'title' => trim($data['title']),
                'amount' => $data['amount'],
                'billing_period' => $data['billing_period'],
                'modules' => $data['modules'] ?? null,
                'scope' => $data['scope'] ?? null,
                'status' => $data['status'],
                'sent_at' => $data['status'] === 'sent'
                    ? (isset($data['sent_at']) ? Carbon::parse($data['sent_at']) : now())
                    : null,
                'valid_until' => $data['valid_until'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->activity($subjectType, $id, (int) $request->user()->id, 'proposal',
                'Propuesta #'.$proposalId.' registrada como '.self::PROPOSAL_STATUSES[$data['status']].'.');
        });

        return back()->with('panel_success', 'Propuesta registrada. El panel no envía propuestas automáticamente.');
    }

    public function updateProposal(Request $request, string $type, int $id, int $proposalId): RedirectResponse
    {
        $this->authorizeOperator($request);
        [$table, $subjectType] = $this->subject($type);
        abort_unless(DB::table($table)->where('id', $id)->exists(), 404);
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::PROPOSAL_STATUSES))],
        ]);

        DB::transaction(function () use ($request, $subjectType, $id, $proposalId, $data): void {
            $proposal = DB::table('commercial_proposals')
                ->where('id', $proposalId)->where('subject_type', $subjectType)->where('subject_id', $id)
                ->lockForUpdate()->first();
            abort_unless($proposal, 404);
            if ($proposal->status === $data['status']) {
                return;
            }

            DB::table('commercial_proposals')->where('id', $proposalId)->update([
                'status' => $data['status'],
                'sent_at' => $data['status'] === 'sent' && ! $proposal->sent_at ? now() : $proposal->sent_at,
                'updated_at' => now(),
            ]);
            $this->activity($subjectType, $id, (int) $request->user()->id, 'proposal',
                'Propuesta #'.$proposalId.': '.self::PROPOSAL_STATUSES[$proposal->status]
                .' → '.self::PROPOSAL_STATUSES[$data['status']].'.');
        });

        return back()->with('panel_success', 'Estado de la propuesta actualizado.');
    }

    private function subject(string $type): array
    {
        return match ($type) {
            'contacto' => ['commercial_leads', 'lead'],
            'solicitud' => ['company_applications', 'application'],
            default => abort(404),
        };
    }

    private function authorizeOperator(Request $request): void
    {
        abort_unless($request->user()?->is_minka_operator, 403);
    }

    private function activity(string $subjectType, int $subjectId, int $operatorId, string $type, string $description): void
    {
        DB::table('commercial_activities')->insert([
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'operator_id' => $operatorId,
            'activity_type' => $type,
            'description' => $description,
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
