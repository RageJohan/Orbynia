<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class CommercialController extends Controller
{
    private const PLANS = ['inicio', 'crecimiento', 'integral'];

    private const RESERVED_SLUGS = [
        'admin', 'api', 'app', 'assets', 'blog', 'cdn', 'contacto', 'correo',
        'gestion', 'info', 'mail', 'panel', 'soporte', 'status', 'www',
    ];

    public function contact(Request $request): View
    {
        return view('public.contact', [
            'brand' => config('orbynia'),
            'source' => in_array($request->query('motivo'), ['demo', 'operacion', 'movil', 'contacto'], true)
                ? $request->query('motivo') : 'contacto',
        ]);
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'source' => ['required', Rule::in(['demo', 'operacion', 'movil', 'contacto'])],
            'company_name' => ['required', 'string', 'max:160'],
            'contact_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:32'],
            'message' => ['nullable', 'string', 'max:3000'],
            'privacy_accept' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ]);

        $id = DB::table('commercial_leads')->insertGetId([
            'source' => $data['source'],
            'company_name' => trim($data['company_name']),
            'contact_name' => trim($data['contact_name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone']),
            'message' => $data['message'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->notifyMinka('Nuevo contacto ORBYNIA #'.$id, 'Se registró un nuevo contacto comercial. Revísalo en el Panel de MINKA.');
        return back()->with('commercial_success', 'Recibimos tu mensaje. Nos comunicaremos contigo.');
    }

    public function evaluation(): View
    {
        return view('public.evaluation', ['brand' => config('orbynia'), 'plans' => self::PLANS]);
    }

    public function slugAvailability(string $slug): JsonResponse
    {
        $slug = strtolower($slug);
        $valid = (bool) preg_match('/^[a-z0-9](?:[a-z0-9-]{1,38})[a-z0-9]$/', $slug)
            && ! in_array($slug, self::RESERVED_SLUGS, true);

        return response()->json([
            'available' => $valid
                && ! DB::table('company_applications')->where('approved_slug', $slug)->exists(),
        ]);
    }

    public function storeEvaluation(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:160'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:32'],
            'requested_slug' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9-]{1,38}[a-z0-9]$/'],
            'plan_interest' => ['required', Rule::in(self::PLANS)],
            'privacy_accept' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ]);

        $slug = strtolower($data['requested_slug']);
        if (in_array($slug, self::RESERVED_SLUGS, true)
            || DB::table('company_applications')->where('approved_slug', $slug)->exists()) {
            return back()->withInput()->withErrors(['requested_slug' => 'Ese subdominio no está disponible.']);
        }

        $uuid = (string) Str::uuid();
        $id = DB::table('company_applications')->insertGetId([
            'uuid' => $uuid,
            'company_name' => trim($data['company_name']),
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone']),
            'requested_slug' => $slug,
            'plan_interest' => $data['plan_interest'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('company_application_events')->insert([
            'application_id' => $id,
            'event' => 'submitted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $verificationUrl = URL::temporarySignedRoute('evaluation.verify', now()->addDay(), ['uuid' => $uuid]);
        try {
            Mail::raw("Hola {$data['first_name']}, confirma tu solicitud de evaluación de ORBYNIA en este enlace (válido por 24 horas):\n{$verificationUrl}\n\nSi no la enviaste, ignora este mensaje.", function ($message) use ($data): void {
                $message->to($data['email'])->subject('Confirma tu solicitud de evaluación ORBYNIA');
            });
        } catch (Throwable $exception) {
            Log::error('No se pudo enviar la verificación comercial', ['application_id' => $id, 'error' => $exception->getMessage()]);
        }

        $this->notifyMinka('Nueva solicitud ORBYNIA #'.$id, 'Hay una solicitud de evaluación pendiente de verificación de correo en el Panel de MINKA.');
        return redirect()->route('evaluation.form')->with('commercial_success', 'Recibimos tu solicitud. Revisa tu correo para confirmarla.');
    }

    public function verifyEvaluation(string $uuid): View
    {
        $application = DB::table('company_applications')->where('uuid', $uuid)->first();
        abort_unless($application, 404);

        if ($application->email_verified_at === null) {
            DB::transaction(function () use ($application): void {
                DB::table('company_applications')->where('id', $application->id)->update([
                    'email_verified_at' => now(),
                    'status' => 'pending_review',
                    'updated_at' => now(),
                ]);
                DB::table('company_application_events')->insert([
                    'application_id' => $application->id,
                    'event' => 'email_verified',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        }

        return view('public.evaluation-confirmed', ['brand' => config('orbynia')]);
    }

    private function notifyMinka(string $subject, string $body): void
    {
        try {
            Mail::raw($body, function ($message) use ($subject): void {
                $message->to(config('orbynia.contact_email'))->subject($subject);
            });
        } catch (Throwable $exception) {
            Log::error('No se pudo avisar a MINKA', ['subject' => $subject, 'error' => $exception->getMessage()]);
        }
    }
}
