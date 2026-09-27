@extends('public.layout')
@section('title', 'Reenviar confirmación | ORBYNIA')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="subpage-hero">
    <div class="container narrow">
        <span class="kicker">EVALUACIÓN ORBYNIA</span>
        <h1>Solicita otro enlace.</h1>
        <p>Si no recibiste el correo de confirmación o el enlace venció, escribe el mismo correo que usaste en la solicitud.</p>
    </div>
</section>
<section class="subpage-body">
    <div class="container narrow">
        <form class="public-form" method="post" action="{{ route('evaluation.resend-email') }}">
            @csrf
            <h2>Reenviar confirmación</h2>
            @if(session('resend_notice'))<div class="form-success" role="status">{{ session('resend_notice') }}</div>@endif
            @if($errors->any())<div class="form-alert" role="alert">{{ $errors->first() }}</div>@endif
            <div class="field">
                <label for="resend-email">Correo de la solicitud</label>
                <input id="resend-email" type="email" name="email" value="{{ old('email') }}" maxlength="190" autocomplete="email" required>
            </div>
            <p class="field-hint">Por privacidad, mostraremos el mismo mensaje aunque ese correo no tenga una solicitud pendiente. El reenvío tiene un intervalo mínimo de un minuto.</p>
            <button class="button button-primary" type="submit">Solicitar reenvío</button>
        </form>
    </div>
</section>
@endsection
