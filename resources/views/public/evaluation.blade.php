@extends('public.layout')
@section('title', 'Solicitar evaluación | ORBYNIA')
@section('content')
<section class="subpage-hero"><div class="container narrow"><span class="kicker">EVALUACIÓN</span><h1>Solicita ORBYNIA para tu empresa.</h1><p>Revisaremos tu solicitud y acordaremos contigo módulos y duración antes de activar una instancia propia.</p></div></section>
<section class="subpage-body"><div class="container form-layout">
    <aside class="form-aside"><h2>Así funciona.</h2><p>1. Completa la solicitud y confirma tu correo.</p><p>2. MINKA se comunica contigo para definir el alcance.</p><p>3. Si se aprueba, recibirás la dirección de tu ERP y una invitación para crear tu clave.</p><p>Este formulario no inicia sesión ni cobra el servicio.</p></aside>
    <form method="post" action="{{ route('evaluation.store') }}" class="public-form evaluation-form" data-evaluation-form data-slug-check="{{ route('evaluation.slug', ['slug' => '__slug__']) }}">
        @csrf
        <h2>Datos para tu evaluación</h2>
        @if(session('commercial_success'))<div class="form-success" role="status">{{ session('commercial_success') }}</div>@endif
        @if($errors->any())<div class="form-alert" role="alert"><p>Revisa los campos señalados:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="form-honeypot" aria-hidden="true"><label for="evaluation-website">Deja esto vacío</label><input id="evaluation-website" name="website" tabindex="-1" autocomplete="off"></div>
        <div class="wizard-progress" aria-label="Progreso de la solicitud"><span>01 Contacto</span><span>02 Interés</span><span>03 Empresa</span></div>
        <fieldset class="wizard-step" data-step="0"><legend>1. ¿Cómo podemos contactarte?</legend>
            <div class="field"><label for="evaluation-phone">Teléfono *</label><input id="evaluation-phone" name="phone" type="tel" value="{{ old('phone') }}" required maxlength="32" autocomplete="tel"></div>
            <p class="field-hint">Lo usaremos para coordinar la evaluación. No activaremos una instancia sin hablar contigo.</p>
        </fieldset>
        <fieldset class="wizard-step" data-step="1"><legend>2. ¿Qué plan te interesa?</legend>
            <p>Estos niveles son orientativos. El alcance y precio se definirán en una propuesta.</p>
            <div class="plan-options">
                @foreach($plans as $plan)
                <label class="plan-option"><input type="radio" name="plan_interest" value="{{ $plan }}" required @checked(old('plan_interest', request('plan')) === $plan)><span><strong>{{ ucfirst($plan) }}</strong><small>ERP y app móvil; módulos según evaluación.</small></span></label>
                @endforeach
            </div>
        </fieldset>
        <fieldset class="wizard-step" data-step="2"><legend>3. Tu empresa y responsable</legend>
            <div class="form-grid">
                <div class="field field-full"><label for="evaluation-company">Nombre de empresa *</label><input id="evaluation-company" name="company_name" value="{{ old('company_name') }}" required maxlength="160"></div>
                <div class="field"><label for="evaluation-first">Nombre *</label><input id="evaluation-first" name="first_name" value="{{ old('first_name') }}" required maxlength="100" autocomplete="given-name"></div>
                <div class="field"><label for="evaluation-last">Apellidos *</label><input id="evaluation-last" name="last_name" value="{{ old('last_name') }}" required maxlength="100" autocomplete="family-name"></div>
                <div class="field field-full"><label for="evaluation-email">Correo de trabajo *</label><input id="evaluation-email" name="email" type="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email"></div>
                <div class="field field-full"><label for="evaluation-slug">Subdominio deseado *</label><div class="slug-input"><input id="evaluation-slug" name="requested_slug" value="{{ old('requested_slug') }}" required minlength="3" maxlength="40" pattern="[a-z0-9][a-z0-9-]{1,38}[a-z0-9]" autocapitalize="none" spellcheck="false"><span>.orbynia.com</span></div><p id="slug-help" class="field-hint" aria-live="polite">El nombre de empresa propone una opción editable. Se confirma al aprobar la solicitud.</p></div>
            </div>
            <label class="check-label"><input type="checkbox" name="privacy_accept" value="1" required @checked(old('privacy_accept'))><span>He leído la <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Política de privacidad</a> y acepto el tratamiento de mis datos para gestionar esta solicitud. *</span></label>
        </fieldset>
        <div class="wizard-actions"><button type="button" class="button button-outline" data-wizard-back hidden>Anterior</button><button type="button" class="button button-primary" data-wizard-next hidden>Siguiente →</button><button class="button button-primary" type="submit" data-wizard-submit>Enviar solicitud ↗</button></div>
    </form>
</div></section>
@endsection