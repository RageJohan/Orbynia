<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MinkaPanelController extends Controller
{
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
        return view('panel.dashboard', [
            'applications' => DB::table('company_applications')->orderByDesc('created_at')->paginate(20, ['*'], 'applications'),
            'leads' => DB::table('commercial_leads')->orderByDesc('created_at')->paginate(15, ['*'], 'leads'),
        ]);
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
            abort_unless($application && $application->status === 'pending_review', 409);
            DB::table('company_applications')->where('id', $id)->update([
                'status' => 'rejected',
                'operator_notes' => $data['reason'],
                'updated_at' => now(),
            ]);
            $this->event($id, (int) $request->user()->id, 'rejected', $data['reason']);
        });

        return back()->with('panel_success', 'Solicitud rechazada.');
    }

    public function updateLead(Request $request, int $id): RedirectResponse
    {
        $this->authorizeOperator($request);
        $data = $request->validate(['status' => ['required', Rule::in(['new', 'contacted', 'closed'])]]);
        DB::table('commercial_leads')->where('id', $id)->update(['status' => $data['status'], 'updated_at' => now()]);
        return back()->with('panel_success', 'Contacto actualizado.');
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
}
