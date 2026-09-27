<?php

namespace App\Http\Controllers;

use App\Support\CompanySubdomain;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class CommercialController extends Controller
{
    private const PLANS = ['inicio', 'crecimiento', 'integral'];
    private const VERIFICATION_RETRY_SECONDS = 60;

    public function __construct(private readonly CompanySubdomain $subdomains) {}

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
        return response()->json([
            'available' => $this->subdomains->isAvailable(strtolower($slug)),
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
        if (! $this->subdomains->isAvailable($slug)) {
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

        $this->sendVerificationEmail($id, false);
        $this->notifyMinka('Nueva solicitud ORBYNIA #'.$id, 'Hay una solicitud de evaluación pendiente de verificación de correo en el Panel de MINKA.');
        return redirect()->route('evaluation.verification-status', $uuid);
    }

    public function verificationStatus(string $uuid): View
    {
        $application = DB::table('company_applications')->where('uuid', $uuid)->first();
        abort_unless($application, 404);

        $lastAttempt = $application->verification_last_attempt_at
            ? Carbon::parse($application->verification_last_attempt_at) : null;
        $retryAt = $lastAttempt?->copy()->addSeconds(self::VERIFICATION_RETRY_SECONDS);
        [$localPart, $domain] = explode('@', $application->email, 2);

        return view('public.evaluation-verification', [
            'brand' => config('orbynia'),
            'application' => $application,
            'maskedEmail' => substr($localPart, 0, 2).str_repeat('*', 4).'@'.$domain,
            'canResend' => $application->status === 'awaiting_verification'
                && ($retryAt === null || $retryAt->isPast()),
            'retrySeconds' => $retryAt && $retryAt->isFuture() ? now()->diffInSeconds($retryAt) : 0,
            'conflictUrl' => $application->status === 'slug_conflict'
                ? URL::temporarySignedRoute('evaluation.verify', now()->addMinutes(10), ['uuid' => $uuid])
                : null,
        ]);
    }

    public function resendVerification(string $uuid): RedirectResponse
    {
        $application = DB::table('company_applications')->where('uuid', $uuid)->first();
        abort_unless($application, 404);

        $result = $this->sendVerificationEmail($application->id, true);
        $message = match ($result) {
            'cooldown' => 'Espera un minuto desde el último intento antes de reenviar.',
            'not_pending' => 'Esta solicitud ya no necesita confirmar el correo.',
            default => 'Se registró el nuevo intento de envío. Consulta el estado actualizado abajo.',
        };

        return redirect()->route('evaluation.verification-status', $uuid)
            ->with('verification_notice', $message);
    }

    public function resendForm(): View
    {
        return view('public.evaluation-resend', ['brand' => config('orbynia')]);
    }

    public function resendByEmail(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:190']]);
        $application = DB::table('company_applications')
            ->where('email', strtolower(trim($data['email'])))
            ->where('status', 'awaiting_verification')
            ->orderByDesc('created_at')->first();

        if ($application) {
            $this->sendVerificationEmail($application->id, true);
        }

        return back()->with('resend_notice', 'Si hay una solicitud pendiente para ese correo, intentamos enviar un nuevo enlace. Comprueba también la carpeta de correo no deseado.');
    }

    public function verifyEvaluation(string $uuid): View
    {
        $application = DB::table('company_applications')->where('uuid', $uuid)->first();
        abort_unless($application, 404);

        if ($application->status === 'awaiting_verification') {
            try {
                DB::transaction(function () use ($application): void {
                    $current = DB::table('company_applications')->where('id', $application->id)->lockForUpdate()->first();
                    if ($current->status !== 'awaiting_verification') {
                        return;
                    }

                    if (! $this->subdomains->isAvailable($current->requested_slug)) {
                        $this->markSlugConflict($current->id);
                        return;
                    }

                    DB::table('company_applications')->where('id', $current->id)->update([
                        'email_verified_at' => now(),
                        'reserved_slug' => $current->requested_slug,
                        'status' => 'pending_review',
                        'updated_at' => now(),
                    ]);
                    $this->applicationEvent($current->id, 'email_verified', 'Correo confirmado.');
                    $this->applicationEvent($current->id, 'slug_reserved', 'Subdominio reservado durante la revisión.');
                });
            } catch (QueryException $exception) {
                $current = DB::table('company_applications')->where('id', $application->id)->first();
                if ($current->status === 'awaiting_verification'
                    && ! $this->subdomains->isAvailable($current->requested_slug)) {
                    DB::transaction(function () use ($current): void {
                        $locked = DB::table('company_applications')->where('id', $current->id)->lockForUpdate()->first();
                        if ($locked->status === 'awaiting_verification') {
                            $this->markSlugConflict($locked->id);
                        }
                    });
                } elseif ($current->status === 'awaiting_verification') {
                    throw $exception;
                }
            }
        }

        $application = DB::table('company_applications')->where('id', $application->id)->first();
        if ($application->status === 'slug_conflict') {
            return view('public.evaluation-conflict', [
                'brand' => config('orbynia'),
                'application' => $application,
                'changeUrl' => URL::temporarySignedRoute('evaluation.slug.change', now()->addDays(2), ['uuid' => $uuid]),
            ]);
        }

        if ($application->status === 'rejected') {
            return view('public.evaluation-closed', ['brand' => config('orbynia')]);
        }

        return view('public.evaluation-confirmed', ['brand' => config('orbynia')]);
    }

    public function changeRequestedSlug(Request $request, string $uuid): RedirectResponse
    {
        $data = $request->validate([
            'requested_slug' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9-]{1,38}[a-z0-9]$/'],
        ]);
        $slug = strtolower($data['requested_slug']);

        if (! $this->subdomains->isAvailable($slug)) {
            return back()->withInput()->withErrors(['requested_slug' => 'Ese subdominio ya está reservado o no se puede utilizar.']);
        }

        try {
            DB::transaction(function () use ($uuid, $slug): void {
                $application = DB::table('company_applications')->where('uuid', $uuid)->lockForUpdate()->first();
                abort_unless($application && $application->email_verified_at && $application->status === 'slug_conflict', 409);

                if (! $this->subdomains->isAvailable($slug, $application->id)) {
                    throw ValidationException::withMessages([
                        'requested_slug' => 'Ese subdominio ya está reservado. Elige otro.',
                    ]);
                }

                DB::table('company_applications')->where('id', $application->id)->update([
                    'requested_slug' => $slug,
                    'reserved_slug' => $slug,
                    'status' => 'pending_review',
                    'updated_at' => now(),
                ]);
                $this->applicationEvent($application->id, 'slug_reserved', 'Se eligió y reservó otro subdominio.');
            });
        } catch (QueryException $exception) {
            if (! $this->subdomains->isAvailable($slug)) {
                return back()->withInput()->withErrors(['requested_slug' => 'Ese subdominio acaba de reservarse. Elige otro.']);
            }
            throw $exception;
        }

        return redirect()->route('evaluation.confirmed')->with('subdomain_reserved', true);
    }

    public function confirmed(): View|RedirectResponse
    {
        if (! session('subdomain_reserved')) {
            return redirect()->route('evaluation.form');
        }

        return view('public.evaluation-confirmed', ['brand' => config('orbynia')]);
    }

    private function markSlugConflict(int $applicationId): void
    {
        DB::table('company_applications')->where('id', $applicationId)->update([
            'email_verified_at' => now(),
            'status' => 'slug_conflict',
            'updated_at' => now(),
        ]);
        $this->applicationEvent($applicationId, 'email_verified', 'Correo confirmado.');
        $this->applicationEvent($applicationId, 'slug_conflict', 'El subdominio solicitado fue reservado por otra empresa.');
    }

    private function applicationEvent(int $applicationId, string $event, string $details): void
    {
        DB::table('company_application_events')->insert([
            'application_id' => $applicationId,
            'event' => $event,
            'details' => $details,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function sendVerificationEmail(int $applicationId, bool $respectCooldown): string
    {
        $application = DB::transaction(function () use ($applicationId, $respectCooldown) {
            $current = DB::table('company_applications')->where('id', $applicationId)->lockForUpdate()->first();
            if (! $current || $current->status !== 'awaiting_verification') {
                return 'not_pending';
            }
            if ($respectCooldown && $current->verification_last_attempt_at
                && Carbon::parse($current->verification_last_attempt_at)
                    ->addSeconds(self::VERIFICATION_RETRY_SECONDS)->isFuture()) {
                return 'cooldown';
            }

            DB::table('company_applications')->where('id', $applicationId)->update([
                'verification_mail_status' => 'sending',
                'verification_last_attempt_at' => now(),
                'verification_attempts' => $current->verification_attempts + 1,
                'updated_at' => now(),
            ]);
            return $current;
        });

        if (is_string($application)) {
            return $application;
        }

        $verificationUrl = URL::temporarySignedRoute('evaluation.verify', now()->addDay(), ['uuid' => $application->uuid]);
        try {
            Mail::raw("Hola {$application->first_name}, confirma tu solicitud de evaluación de ORBYNIA en este enlace (válido por 24 horas):\n{$verificationUrl}\n\nAl confirmar, intentaremos reservar el subdominio solicitado. Si otra empresa lo reservó primero, podrás escoger otro.\n\nSi no la enviaste, ignora este mensaje.", function ($message) use ($application): void {
                $message->to($application->email)->subject('Confirma tu solicitud de evaluación ORBYNIA');
            });
        } catch (Throwable $exception) {
            DB::table('company_applications')->where('id', $applicationId)->update([
                'verification_mail_status' => 'failed',
                'verification_sent_at' => null,
                'updated_at' => now(),
            ]);
            Log::error('No se pudo enviar la verificación comercial', [
                'application_id' => $applicationId,
                'error' => $exception->getMessage(),
            ]);
            return 'failed';
        }

        $status = in_array(config('mail.default'), ['log', 'array'], true) ? 'logged' : 'sent';
        DB::table('company_applications')->where('id', $applicationId)->update([
            'verification_mail_status' => $status,
            'verification_sent_at' => $status === 'sent' ? now() : null,
            'updated_at' => now(),
        ]);
        return $status;
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
