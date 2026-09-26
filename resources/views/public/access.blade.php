@extends('public.layout')
@section('title', 'Acceder a ORBYNIA')
@section('description', 'Encuentra el acceso de tu empresa o entra al Panel de MINKA.')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="access-section">
    <div class="container access-shell">
        <div class="access-heading">
            <span class="kicker">TU ESPACIO ORBYNIA</span>
            <h1>Ingresa a tu empresa.</h1>
            <p>Escribe el código de tu empresa o el correo de su primer ADMIN. Abriremos su instancia en otra pestaña para que inicies sesión allí.</p>
        </div>

        <div class="access-grid">
            <div class="access-card access-card-main">
                <span class="access-card-number">01 / YA USAS ORBYNIA</span>
                <h2>Encuentra tu acceso</h2>
                <p>El equipo de MINKA también puede comenzar aquí con su correo; la contraseña se solicita en el Panel de MINKA.</p>
                @if($errors->any())
                    <div class="form-alert" role="alert">{{ $errors->first() }}</div>
                @endif
                <form method="post" action="{{ route('access.resolve') }}" target="_blank" rel="noopener noreferrer" class="access-form">
                    @csrf
                    <label for="identifier">Código de empresa, usuario MINKA o correo</label>
                    <input id="identifier" name="identifier" type="text" value="{{ old('identifier') }}" placeholder="empresa o admin@empresa.com" maxlength="190" autocomplete="username" required>
                    <button class="button button-primary" type="submit">Continuar <span aria-hidden="true">↗</span></button>
                </form>
                <p class="access-note">La landing no recibe ni verifica la contraseña del ERP. La ingresarás únicamente en el dominio de tu empresa.</p>
                @if(app()->environment('local'))
                    <p class="access-local">En desarrollo local, escribe <strong>{{ config('orbynia.local_client_slug') }}</strong> para abrir el ERP local de Cobeles.</p>
                @endif
            </div>

            <aside class="access-card access-card-new">
                <span class="access-card-number">02 / NUEVA EMPRESA</span>
                <h2>¿Quieres solicitar ORBYNIA?</h2>
                <p>Cuéntanos sobre tu empresa y el plan que te interesa. MINKA revisará tu solicitud antes de preparar una instancia.</p>
                <a class="button button-outline" href="{{ route('evaluation.form') }}">Solicitar el servicio <span aria-hidden="true">↗</span></a>
                <a class="access-contact" href="{{ route('contact.form') }}">Prefiero hacer una consulta primero</a>
            </aside>
        </div>
    </div>
</section>
@endsection
