@extends('public.layout')
@section('title', 'Elige otro subdominio | ORBYNIA')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="subpage-hero">
    <div class="container narrow">
        <span class="kicker">CORREO CONFIRMADO</span>
        <h1>Elige otra dirección para tu empresa.</h1>
        <p>Otra empresa reservó <strong>{{ $application->requested_slug }}.orbynia.com</strong> antes de que confirmaras tu correo. Tu solicitud sigue registrada.</p>
    </div>
</section>
<section class="subpage-body">
    <div class="container narrow">
        <form class="public-form" method="post" action="{{ $changeUrl }}" data-slug-replacement data-slug-check="{{ route('evaluation.slug', ['slug' => '__slug__']) }}">
            @csrf
            <h2>Nuevo subdominio</h2>
            <p>Escribe un nombre distinto. Si sigue libre al guardar, quedará reservado para tu solicitud durante la revisión de MINKA. Todavía no se creará el ERP.</p>
            @if($errors->any())<div class="form-alert" role="alert">{{ $errors->first() }}</div>@endif
            <div class="field">
                <label for="replacement-slug">Nombre de tu empresa en ORBYNIA *</label>
                <div class="slug-input">
                    <input id="replacement-slug" name="requested_slug" value="{{ old('requested_slug') }}" required minlength="3" maxlength="40" pattern="[a-z0-9][a-z0-9-]{1,38}[a-z0-9]" autocapitalize="none" spellcheck="false">
                    <span>.orbynia.com</span>
                </div>
                <p id="replacement-slug-help" class="field-hint" aria-live="polite">La disponibilidad se confirma al guardar.</p>
            </div>
            <button class="button button-primary" type="submit">Reservar este nombre <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</section>
@endsection
