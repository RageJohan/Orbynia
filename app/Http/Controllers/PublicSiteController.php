<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class PublicSiteController extends Controller
{
    private function shared(): array
    {
        return [
            'brand' => config('orbynia'),
        ];
    }

    public function home(): View
    {
        return view('public.home', $this->shared());
    }

    public function terms(): View
    {
        return view('public.terms', $this->shared());
    }

    public function privacy(): View
    {
        return view('public.privacy', $this->shared());
    }

    public function complaintForm(): View
    {
        return view('public.complaint', $this->shared());
    }

    public function storeComplaint(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'document_type' => ['required', 'in:DNI,CE,Pasaporte,RUC'],
            'document_number' => ['required', 'string', 'max:24'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:32'],
            'is_minor' => ['required', 'boolean'],
            'guardian_name' => ['required_if:is_minor,1', 'nullable', 'string', 'max:180'],
            'guardian_document' => ['required_if:is_minor,1', 'nullable', 'string', 'max:40'],
            'address' => ['required', 'string', 'max:300'],
            'item_type' => ['required', 'in:servicio,producto'],
            'item_description' => ['required', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'complaint_type' => ['required', 'in:reclamo,queja'],
            'description' => ['required', 'string', 'min:10', 'max:10000'],
            'requested_resolution' => ['required', 'string', 'min:5', 'max:10000'],
            'privacy_accept' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ]);

        $token = Str::random(48);
        $reference = DB::transaction(function () use ($data, $token): string {
            $id = DB::table('public_complaints')->insertGetId([
                'receipt_token' => $token,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'document_type' => $data['document_type'],
                'document_number' => $data['document_number'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'is_minor' => $data['is_minor'],
                'guardian_name' => $data['guardian_name'] ?? null,
                'guardian_document' => $data['guardian_document'] ?? null,
                'address' => $data['address'],
                'item_type' => $data['item_type'],
                'item_description' => $data['item_description'],
                'amount' => $data['amount'],
                'complaint_type' => $data['complaint_type'],
                'description' => $data['description'],
                'requested_resolution' => $data['requested_resolution'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $reference = 'ORB-'.now()->format('Y').'-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
            DB::table('public_complaints')->where('id', $id)->update(['reference' => $reference]);

            return $reference;
        });

        $receiptUrl = route('complaints.receipt', ['token' => $token]);
        $this->sendMail($data['email'], 'Constancia de reclamación '.$reference,
            "Hemos recibido tu hoja de reclamación {$reference}.\nPuedes consultar e imprimir tu constancia en: {$receiptUrl}");
        $this->sendMail(config('orbynia.contact_email'), 'Nueva reclamación '.$reference,
            "Se registró la hoja {$reference}. Revisa el Libro de Reclamaciones en la base de datos de ORBYNIA.");

        return redirect()->route('complaints.receipt', ['token' => $token]);
    }

    public function complaintReceipt(string $token): View
    {
        $complaint = DB::table('public_complaints')->where('receipt_token', $token)->first();
        abort_unless($complaint, 404);

        return view('public.receipt', array_merge($this->shared(), ['complaint' => $complaint]));
    }

    public function deletionForm(): View
    {
        return view('public.deletion', $this->shared());
    }

    public function storeDeletionRequest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company' => ['required', 'string', 'max:120'],
            'full_name' => ['required', 'string', 'max:180'],
            'email' => ['required', 'email', 'max:190'],
            'account_identifier' => ['required', 'string', 'max:190'],
            'details' => ['nullable', 'string', 'max:5000'],
            'privacy_accept' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ]);

        $reference = DB::transaction(function () use ($data): string {
            $id = DB::table('account_deletion_requests')->insertGetId([
                'company' => $data['company'],
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'account_identifier' => $data['account_identifier'],
                'details' => $data['details'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $reference = 'ELI-'.now()->format('Y').'-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
            DB::table('account_deletion_requests')->where('id', $id)->update(['reference' => $reference]);

            return $reference;
        });

        $this->sendMail($data['email'], 'Solicitud de eliminación '.$reference,
            "Recibimos tu solicitud {$reference}. Te contactaremos para verificar la titularidad y explicarte los datos que deban conservarse por obligación legal.");
        $this->sendMail(config('orbynia.contact_email'), 'Nueva solicitud de eliminación '.$reference,
            "Se registró la solicitud {$reference}. Revisa la base de datos de ORBYNIA para gestionarla.");

        return redirect()->route('deletion.form')->with('reference', $reference);
    }

    private function sendMail(string $address, string $subject, string $body): void
    {
        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return;
        }

        try {
            Mail::raw($body, function ($message) use ($address, $subject): void {
                $message->to($address)->subject($subject);
            });
        } catch (Throwable $exception) {
            Log::error('No se pudo enviar un correo público de ORBYNIA', [
                'subject' => $subject,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}