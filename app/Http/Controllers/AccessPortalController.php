<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessPortalController extends Controller
{
    public function index(): View
    {
        return view('public.access', ['brand' => config('orbynia')]);
    }

    public function resolve(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:190'],
        ]);

        $identifier = strtolower(trim($data['identifier']));

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $isOperator = DB::table('users')
                ->where('email', $identifier)
                ->where('is_minka_operator', true)
                ->exists();

            if ($isOperator) {
                return $request->user()?->is_minka_operator
                    ? redirect()->route('minka.dashboard')
                    : redirect()->route('minka.login', ['email' => $identifier]);
            }

            $slugs = DB::table('company_applications')
                ->where('admin_email', $identifier)
                ->where('status', 'active')
                ->limit(2)
                ->pluck('approved_slug');

            if ($slugs->count() === 1) {
                return $this->redirectToCompany((string) $slugs->first());
            }

            if ($slugs->count() > 1) {
                return back()->withInput()->withErrors([
                    'identifier' => 'Este correo corresponde a varias empresas. Ingresa el código de la empresa.',
                ]);
            }
        } elseif (preg_match('/^[a-z0-9][a-z0-9-]{1,38}[a-z0-9]$/', $identifier)) {
            if (app()->environment('local', 'testing')
                && $identifier === config('orbynia.local_client_slug')) {
                return redirect()->away(config('orbynia.local_client_url'));
            }

            $active = DB::table('company_applications')
                ->where('approved_slug', $identifier)
                ->where('status', 'active')
                ->exists();

            if ($active) {
                return $this->redirectToCompany($identifier);
            }
        }

        $operatorEmails = DB::table('users')
            ->where('is_minka_operator', true)
            ->whereRaw('LOWER(name) = ?', [$identifier])
            ->limit(2)
            ->pluck('email');

        if ($operatorEmails->count() === 1) {
            return $request->user()?->is_minka_operator
                ? redirect()->route('minka.dashboard')
                : redirect()->route('minka.login', ['email' => $operatorEmails->first()]);
        }

        return back()->withInput()->withErrors([
            'identifier' => 'No encontramos una empresa activa con ese dato. Revisa el código recibido o contacta a MINKA.',
        ]);
    }

    private function redirectToCompany(string $slug): RedirectResponse
    {
        return redirect()->away('https://'.$slug.'.'.config('orbynia.domain').'/');
    }
}
