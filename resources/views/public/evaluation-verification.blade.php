@extends('public.layout')
@section('title', 'Confirma tu correo | ORBYNIA')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="subpage-hero">
    <div class="container narrow">
        <span class="kicker">EVALUACIÓN ORBYNIA</span>
        <h1>Confirma tu correo.</h1>
        <p>Solicitud de {{ $application->company_name }} · {{ $maskedEmail }}</p>
    </div>
</section>
<section class="subpage-body">
    <div class="container narrow">
        <div class="panel-card verification-card">
            @if(session('verification_notice'))<div class="form-success" role="status">{{ session('verification_notice') }}</div>@endif

            @if($application->status === 'slug_conflict')
                <h2>Elige otro subdominio</h2>
                <p>Tu correo está confirmado, pero el nombre solicitado fue reservado antes por otra empresa. Puedes continuar con un nombre diferente.</p>
                <p><a class="button button-primary" href="{{ $conflictUrl }}">Elegir otro nombre</a></p>
            @elseif($application->status === 'rejected')
                <h2>Solicitud cerrada</h2>
                <p>Esta solicitud ya no está vigente. Si quieres continuar, <a href="{{ route('evaluation.form') }}">presenta una nueva solicitud</a>.</p>
            @elseif($application->email_verified_at)
                <h2>Correo confirmado</h2>
                <p>Tu solicitud ya pasó la verificación. MINKA revisará los siguientes pasos; este formulario no activa el ERP.</p>
            @elseif($application->verification_mail_status === 'failed')
                <h2>No pudimos enviar el enlace</h2>
                <p>La solicitud quedó guardada. Puedes pedir otro intento desde esta página o escribirnos a <a href="mailto:{{ $brand['contact_email'] }}">{{ $brand['contact_email'] }}</a>.</p>
            @elseif($application->verification_mail_status === 'logged')
                <h2>El enlace aún no se entregó por correo</h2>
                @if(app()->environment('local', 'testing'))
                    <p>Este entorno local registra los correos en el log de Laravel. El operador puede abrir allí el enlace de confirmación.</p>
                @else
                    <p>El correo está configurado en modo de registro y no llegará a la bandeja de entrada. Contacta a MINKA para continuar.</p>
                @endif
            @elseif($application->verification_mail_status === 'sent')
                <h2>Enlace enviado</h2>
                <p>El servidor de correo aceptó el envío del enlace a {{ $maskedEmail }}. Revisa tu bandeja de entrada y la carpeta de correo no deseado. El enlace vence en 24 horas.</p>
            @else
                <h2>Envío sin confirmar</h2>
                <p>La solicitud quedó guardada, pero aún no consta un envío de verificación. Puedes solicitar otro intento cuando esté disponible.</p>
            @endif

            @if($application->status === 'awaiting_verification')
                @if($canResend)
                    <form method="post" action="{{ route('evaluation.verification-resend', $application->uuid) }}">
                        @csrf
                        <button class="button button-primary" type="submit">Reenviar enlace de verificación</button>
                    </form>
                @else
                    <p class="field-hint">Podrás pedir otro intento en aproximadamente {{ max(1, (int) ceil($retrySeconds)) }} segundos.</p>
                @endif
            @endif
            <p><a href="{{ route('evaluation.resend-form') }}">Buscar una solicitud pendiente con mi correo</a></p>
        </div>
    </div>
</section>
@endsection
