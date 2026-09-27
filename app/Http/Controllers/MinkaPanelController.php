<?php

namespace App\Http\Controllers;

use App\Support\CompanySubdomain;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class MinkaPanelController extends Controller
{
    public function __construct(private readonly CompanySubdomain $subdomains) {}

    private const CASE_STATUSES = [
        'complaint' => [
            'received' => 'Recibida',
            'in_review' => 'En revisión',
            'answered' => 'Respondida',
            'closed' => 'Cerrada',
        ],
        'deletion' => [
            'received' => 'Recibida',
            'verifying' => 'Verificando titularidad',
            'coordinating' => 'Coordinando con la empresa',
            'completed' => 'Completada',
            'rejected' => 'No procede',
        ],
    ];

    public const MODULES = [
        'ventas' => 'Ventas y pedidos',
        'inventario' => 'Inventario',
        'reparto' => 'Reparto y rutas',
        'reportes' => 'Reportes',
        'facturacion' => 'Facturación electrónica',
    ];

    public function loginForm(): View
    {
        return view('panel.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials + ['is_minka_operator' => true])) {
            return back()->withErrors(['email' => 'Acceso no válido.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->route('minka.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('minka.login');
    }

    public function dashboard(Request $request): View
    {
        $this->authorizeOperator($request);
        $until = now()->addDays(7);
        $dueLeads = DB::table('commercial_leads')
            ->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', $until)
            ->whereNotIn('status', ['won', 'lost', 'closed'])
            ->orderBy('next_follow_up_at')->limit(30)
            ->get(['id', 'company_name', 'next_follow_up_at'])
            ->map(fn ($item) => (object) [
                'type' => 'contacto', 'id' => $item->id,
                'company_name' => $item->company_name, 'next_follow_up_at' => $item->next_follow_up_at,
            ]);
        $dueApplications = DB::table('company_applications')
            ->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', $until)
            ->whereNotIn('sales_stage', ['won', 'lost', 'closed'])
            ->orderBy('next_follow_up_at')->limit(30)
            ->get(['id', 'company_name', 'next_follow_up_at'])
            ->map(fn ($item) => (object) [
                'type' => 'solicitud', 'id' => $item->id,
                'company_name' => $item->company_name, 'next_follow_up_at' => $item->next_follow_up_at,
            ]);

        return view('panel.dashboard', [
            'dueFollowUps' => $dueLeads->concat($dueApplications)->sortBy('next_follow_up_at')->take(20),
            'salesStages' => SalesTrackingController::STAGES,
            'applications' => DB::table('company_applications')
                ->leftJoin('users as sales_owner', 'sales_owner.id', '=', 'company_applications.sales_owner_id')
                ->select('company_applications.*', 'sales_owner.name as sales_owner_name')
                ->orderByDesc('company_applications.created_at')->paginate(20, ['*'], 'applications'),
            'leads' => DB::table('commercial_leads')
                ->leftJoin('users as sales_owner', 'sales_owner.id', '=', 'commercial_leads.sales_owner_id')
                ->select('commercial_leads.*', 'sales_owner.name as sales_owner_name')
                ->orderByDesc('commercial_leads.created_at')->paginate(15, ['*'], 'leads'),
            'complaints' => DB::table('public_complaints')
                ->leftJoin('users as assigned_user', 'assigned_user.id', '=', 'public_complaints.responsible_operator_id')
                ->select('public_complaints.*', 'assigned_user.name as responsible_name')
                ->orderByDesc('public_complaints.created_at')->paginate(15, ['*'], 'complaints'),
            'deletionRequests' => DB::table('account_deletion_requests')
                ->leftJoin('users as assigned_user', 'assigned_user.id', '=', 'account_deletion_requests.responsible_operator_id')
                ->select('account_deletion_requests.*', 'assigned_user.name as responsible_name')
                ->orderByDesc('account_deletion_requests.created_at')->paginate(15, ['*'], 'deletions'),
            'caseStatuses' => self::CASE_STATUSES,
        ]);
    }

    public function publicCase(Request $request, int $id, string $type): View
    {
        $this->authorizeOperator($request);
        abort_unless(isset(self::CASE_STATUSES[$type]), 404);

        $table = $type === 'complaint' ? 'public_complaints' : 'account_deletion_requests';
        $case = DB::table($table)->where('id', $id)->first();
        abort_unless($case, 404);

        $events = DB::table('public_request_events')
            ->leftJoin('users', 'users.id', '=', 'public_request_events.operator_id')
            ->where('request_type', $type)
            ->where('request_id', $id)
            ->orderByDesc('public_request_events.id')
            ->select('public_request_events.*', 'users.name as operator_name')
            ->get();

        return view('panel.public-case', [
            'type' => $type,
            'case' => $case,
            'statuses' => self::CASE_STATUSES[$type],
            'operators' => DB::table('users')->where('is_minka_operator', true)->orderBy('name')->get(['id', 'name', 'email']),
            'events' => $events,
            'backRoute' => route('minka.dashboard').'#'.($type === 'complaint' ? 'reclamaciones' : 'eliminaciones'),
            'updateRoute' => $type === 'complaint'
                ? route('minka.complaint.update', $id)
                : route('minka.deletion.update', $id),
        ]);
    }

    public function updatePublicCase(Request $request, int $id, string $type): RedirectResponse
    {
        $this->authorizeOperator($request);
        abort_unless(isset(self::CASE_STATUSES[$type]), 404);

        $table = $type === 'complaint' ? 'public_complaints' : 'account_deletion_requests';
        $case = DB::table($table)->where('id', $id)->first();
        abort_unless($case, 404);

        $rules = [
            'status' => ['required', Rule::in(array_keys(self::CASE_STATUSES[$type]))],
            'responsible_operator_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_minka_operator', true)),
            ],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
            'response' => ['nullable', 'string', 'max:10000'],
            'send_response' => ['sometimes', 'boolean'],
        ];
        if ($type === 'complaint') {
            $rules['response'][] = Rule::requiredIf(fn () => $request->input('status') === 'answered');
        }

        $data = $request->validate($rules);
        $responseColumn = $type === 'complaint' ? 'provider_actions' : 'response';
        $oldResponse = $case->{$responseColumn} ?? '';
        $newResponse = trim((string) ($data['response'] ?? ''));
        $sendResponse = $request->boolean('send_response') && $newResponse !== '';

        if ($type === 'complaint' && $data['status'] === 'answered' && $newResponse === '') {
            return back()->withInput()->withErrors(['response' => 'Registra la respuesta antes de marcar la reclamación como respondida.']);
        }

        $oldResponsible = $case->responsible_operator_id ?? null;
        $newResponsible = $data['responsible_operator_id'] ?? null;
        $oldNotes = $case->internal_notes ?? '';
        $newNotes = trim((string) ($data['internal_notes'] ?? ''));
        $details = [];
        if ($case->status !== $data['status']) {
            $details[] = 'Estado: '.(self::CASE_STATUSES[$type][$case->status] ?? $case->status).' → '.self::CASE_STATUSES[$type][$data['status']];
        }
        if ((string) $oldResponsible !== (string) $newResponsible) {
            $details[] = $newResponsible === null ? 'Se quitó la asignación.' : 'Se asignó un responsable.';
        }
        if ($oldNotes !== $newNotes) {
            $details[] = 'Se actualizaron las notas internas.';
        }
        if ($oldResponse !== $newResponse) {
            $details[] = 'Se actualizó la respuesta al solicitante.';
        }

        DB::transaction(function () use ($table, $case, $type, $id, $request, $data, $responseColumn, $newResponse, $details): void {
            $update = [
                'status' => $data['status'],
                'responsible_operator_id' => $data['responsible_operator_id'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'updated_at' => now(),
            ];

            if ($type === 'complaint') {
                $update['provider_actions'] = $newResponse !== '' ? $newResponse : null;
                $update['responded_at'] = $data['status'] === 'answered'
                    ? ($case->responded_at ?? now())
                    : $case->responded_at;
            } else {
                $update['response'] = $newResponse !== '' ? $newResponse : null;
            }
            if (($case->{$responseColumn} ?? '') !== $newResponse) {
                $update['response_sent_at'] = null;
            }

            DB::table($table)->where('id', $id)->update($update);

            foreach ($details as $detail) {
                $this->publicRequestEvent($type, $id, (int) $request->user()->id, 'updated', $detail);
            }
        });

        if ($sendResponse) {
            $recipient = $type === 'complaint' && $case->is_minor && $case->guardian_email
                ? $case->guardian_email
                : $case->email;
            $reference = $case->reference ?: ($type === 'complaint' ? 'Reclamación #'.$id : 'Solicitud #'.$id);
            $subject = ($type === 'complaint' ? 'Respuesta a tu reclamación ' : 'Respuesta a tu solicitud de eliminación ').$reference;
            $body = "Hola,\n\nEsta es la respuesta de ORBYNIA a {$reference}:\n\n{$newResponse}\n\nSi necesitas hacer una consulta, responde a este correo o contáctanos en ".config('orbynia.contact_email').'.';

            if (in_array(config('mail.default'), ['log', 'array'], true)) {
                return back()->with('panel_warning', 'Los cambios se guardaron, pero el correo no se envió porque el transporte configurado es local.');
            }

            try {
                Mail::raw($body, function ($message) use ($recipient, $subject): void {
                    $message->to($recipient)->subject($subject);
                });
            } catch (Throwable $exception) {
                Log::error('No se pudo enviar una respuesta de ORBYNIA', [
                    'request_type' => $type,
                    'request_id' => $id,
                    'error' => $exception->getMessage(),
                ]);
                return back()->with('panel_warning', 'Los cambios se guardaron, pero no se pudo enviar el correo. Puedes reintentar el envío.');
            }

            DB::table($table)->where('id', $id)->update(['response_sent_at' => now()]);
            $this->publicRequestEvent($type, $id, (int) $request->user()->id, 'response_sent', 'Se envió la respuesta por correo.');
            return back()->with('panel_success', 'Cambios guardados y respuesta enviada por correo.');
        }

        if ($details !== []) {
            return back()->with('panel_success', 'La gestión del caso se actualizó.');
        }

        return back()->with('panel_success', 'No había cambios que guardar.');
    }

    public function show(Request $request, int $id): View
    {
        $this->authorizeOperator($request);
        $application = DB::table('company_applications')->where('id', $id)->first();
        abort_unless($application, 404);

        return view('panel.application', [
            'application' => $application,
            'events' => DB::table('company_application_events')->where('application_id', $id)->orderByDesc('id')->get(),
            'modules' => self::MODULES,
            'selectedModules' => json_decode($application->enabled_modules ?? '[]', true) ?: [],
        ]);
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $this->authorizeOperator($request);
        $data = $request->validate([
            'evaluation_ends_at' => ['required', 'date', 'after:today'],
            'enabled_modules' => ['required', 'array', 'min:1'],
            'enabled_modules.*' => ['required', Rule::in(array_keys(self::MODULES))],
            'operator_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        try {
            DB::transaction(function () use ($request, $id, $data): void {
                $application = DB::table('company_applications')->where('id', $id)->lockForUpdate()->first();
                abort_unless($application && $application->status === 'pending_review' && $application->email_verified_at, 409);
                if ($application->reserved_slug !== $application->requested_slug
                    || ! $this->subdomains->isAvailable($application->requested_slug, $application->id)) {
                    throw ValidationException::withMessages([
                        'slug' => 'La solicitud no tiene ese subdominio reservado. Revisa el estado antes de aprobar.',
                    ]);
                }

                DB::table('company_applications')->where('id', $id)->update([
                    'approved_slug' => $application->requested_slug,
                    'status' => 'approved',
                    'evaluation_ends_at' => $data['evaluation_ends_at'],
                    'enabled_modules' => json_encode(array_values(array_unique($data['enabled_modules']))),
                    'operator_notes' => $data['operator_notes'] ?? null,
                    'updated_at' => now(),
                ]);
                $this->event($id, (int) $request->user()->id, 'approved', 'Instancia pendiente de aprovisionamiento asistido.');
            });
        } catch (QueryException $exception) {
            return back()->withErrors(['slug' => 'El subdominio ya fue aprobado para otra empresa.']);
        }

        return back()->with('panel_success', 'Solicitud aprobada. Ahora prepara la instancia y ejecuta minka:create-admin dentro de ella.');
    }

    public function activate(Request $request, int $id): RedirectResponse
    {
        $this->authorizeOperator($request);
        $data = $request->validate([
            'instance_ready' => ['accepted'],
            'admin_email' => ['required', 'email', 'max:190'],
        ]);

        DB::transaction(function () use ($request, $id, $data): void {
            $application = DB::table('company_applications')->where('id', $id)->lockForUpdate()->first();
            abort_unless($application && $application->status === 'approved', 409);
            DB::table('company_applications')->where('id', $id)->update([
                'status' => 'active',
                'admin_email' => strtolower(trim($data['admin_email'])),
                'activated_at' => now(),
                'updated_at' => now(),
            ]);
            $this->event($id, (int) $request->user()->id, 'activated', 'Operador confirmó instancia, módulos e invitación al ADMIN del ERP.');
        });

        return back()->with('panel_success', 'Instancia marcada como activa.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $this->authorizeOperator($request);
        $data = $request->validate(['reason' => ['required', 'string', 'max:1000']]);

        DB::transaction(function () use ($request, $id, $data): void {
            $application = DB::table('company_applications')->where('id', $id)->lockForUpdate()->first();
            abort_unless($application && in_array($application->status, ['pending_review', 'slug_conflict'], true), 409);
            DB::table('company_applications')->where('id', $id)->update([
                'status' => 'rejected',
                'reserved_slug' => null,
                'operator_notes' => $data['reason'],
                'updated_at' => now(),
            ]);
            $this->event($id, (int) $request->user()->id, 'rejected', $data['reason'].' Se liberó el subdominio reservado.');
        });

        return back()->with('panel_success', 'Solicitud rechazada.');
    }

    private function authorizeOperator(Request $request): void
    {
        abort_unless($request->user()?->is_minka_operator, 403);
    }

    private function event(int $applicationId, int $operatorId, string $event, string $details): void
    {
        DB::table('company_application_events')->insert([
            'application_id' => $applicationId,
            'operator_id' => $operatorId,
            'event' => $event,
            'details' => $details,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function publicRequestEvent(string $type, int $requestId, int $operatorId, string $event, string $details): void
    {
        DB::table('public_request_events')->insert([
            'request_type' => $type,
            'request_id' => $requestId,
            'operator_id' => $operatorId,
            'event' => $event,
            'details' => $details,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
