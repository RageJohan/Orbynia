@extends('public.layout')
@section('title', 'Conversemos | ORBYNIA')
@section('content')
<section class="subpage-hero"><div class="container narrow"><span class="kicker">CONTACTO</span><h1>Conversemos sobre tu operación.</h1><p>Déjanos tus datos y el equipo de MINKA se comunicará contigo.</p></div></section>
<section class="subpage-body"><div class="container form-layout">
    <aside class="form-aside"><h2>Un primer paso sencillo.</h2><p>Este formulario solicita una conversación o demostración. No crea una instancia ni una cuenta del ERP.</p><p>También puedes escribir a <a href="mailto:{{ $brand['contact_email'] }}">{{ $brand['contact_email'] }}</a>.</p></aside>
    <form method="post" action="{{ route('contact.store') }}" class="public-form">
        @csrf
        <h2>Cuéntanos sobre tu empresa</h2>
        @if(session('commercial_success'))<div class="form-success" role="status">{{ session('commercial_success') }}</div>@endif
        @if($errors->any())<div class="form-alert" role="alert">Revisa los campos señalados.</div>@endif
        <input type="hidden" name="source" value="{{ old('source', $source) }}">
        <div class="form-honeypot" aria-hidden="true"><label for="contact-website">Deja esto vacío</label><input id="contact-website" name="website" tabindex="-1" autocomplete="off"></div>
        <div class="form-grid">
            <div class="field field-full"><label for="contact-company">Empresa *</label><input id="contact-company" name="company_name" value="{{ old('company_name') }}" required maxlength="160"></div>
            <div class="field"><label for="contact-name">Tu nombre *</label><input id="contact-name" name="contact_name" value="{{ old('contact_name') }}" required maxlength="160"></div>
            <div class="field"><label for="contact-phone">Teléfono *</label><input id="contact-phone" name="phone" type="tel" value="{{ old('phone') }}" required maxlength="32"></div>
            <div class="field field-full"><label for="contact-email">Correo *</label><input id="contact-email" name="email" type="email" value="{{ old('email') }}" required maxlength="190"></div>
            <div class="field field-full"><label for="contact-message">¿Qué deseas conocer?</label><textarea id="contact-message" name="message" rows="4" maxlength="3000">{{ old('message') }}</textarea></div>
        </div>
        <label class="check-label"><input type="checkbox" name="privacy_accept" value="1" required @checked(old('privacy_accept'))><span>He leído la <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Política de privacidad</a> y acepto el tratamiento de mis datos para esta consulta. *</span></label>
        <button class="button button-primary" type="submit">Enviar consulta <span aria-hidden="true">↗</span></button>
    </form>
</div></section>
@endsection