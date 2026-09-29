<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#662483">
    <meta name="description" content="@yield('description', 'ORBYNIA conecta inventario, ventas, reparto y equipos de campo en una plataforma de gestión empresarial.')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="ORBYNIA">
    <meta property="og:title" content="@yield('title', 'ORBYNIA | Gestión empresarial conectada')">
    <meta property="og:description" content="@yield('description', 'Una operación conectada, de la oficina al campo.')">
    <meta property="og:image" content="{{ asset('images/orbynia/logo-color.png') }}">
    <title>@yield('title', 'ORBYNIA | Gestión empresarial conectada')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.favicon')
</head>
<body>
    <a class="skip-link" href="#contenido">Saltar al contenido</a>
    <header class="site-header" id="inicio" data-tone="@yield('header-tone', 'light')">
        <div class="container header-inner">
            <a class="brand-link" href="{{ route('home') }}" aria-label="ORBYNIA, ir al inicio">
                <img class="brand-logo brand-logo-color" src="{{ asset('images/orbynia/logo-color.png') }}" alt="ORBYNIA" width="190" height="65">
                <img class="brand-logo brand-logo-light" src="{{ asset('images/orbynia/logo-claro.png') }}" alt="" width="190" height="65" aria-hidden="true">
            </a>
            <button class="menu-toggle" type="button" aria-controls="site-nav" aria-expanded="false" aria-label="Abrir menú">
                <span></span><span></span><span></span>
            </button>
            <nav class="site-nav" id="site-nav" aria-label="Navegación principal">
                <a href="{{ route('home') }}#plataforma">Plataforma</a>
                <a href="{{ route('home') }}#como-funciona">Cómo funciona</a>
                <a href="{{ route('home') }}#movilidad">App móvil</a>
                <a href="{{ route('home') }}#planes">Planes</a>
                <a href="{{ route('home') }}#empresa">Nosotros</a>
                <a class="nav-access" href="{{ route('access.form') }}">Ingresar</a>
                <a class="nav-contact" href="{{ route('contact.form') }}">Contacto</a>
            </nav>
        </div>
    </header>

    <main id="contenido">
        @yield('content')
    </main>

    <footer class="site-footer" data-header-theme="dark">
        <div class="container footer-grid">
            <div class="footer-brand">
                <img src="{{ asset('images/orbynia/logo-claro.png') }}" alt="ORBYNIA" width="210" height="72">
                <p>Una operación conectada, de la oficina al campo.</p>
                <a href="mailto:{{ $brand['contact_email'] }}">{{ $brand['contact_email'] }}</a>
            </div>
            <div>
                <h2>Explorar</h2>
                <a href="{{ route('home') }}#plataforma">Plataforma</a>
                <a href="{{ route('home') }}#como-funciona">Cómo funciona</a>
                <a href="{{ route('home') }}#planes">Planes</a>
                <a href="{{ route('access.form') }}">Ingresar</a>
            </div>
            <div>
                <h2>Información legal</h2>
                <a href="{{ route('terms') }}">Términos y condiciones</a>
                <a href="{{ route('complaints.form') }}">Libro de Reclamaciones</a>
                <a href="{{ route('privacy') }}">Política de privacidad</a>
            </div>
            <div class="footer-company">
                <h2>Titular del software</h2>
                <p>{{ $brand['legal_name'] }}<br>RUC {{ $brand['ruc'] }}</p>
                <p>{{ $brand['address'] }}</p>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>© {{ date('Y') }} ORBYNIA. Todos los derechos reservados.</span>
            <span>Desarrollado y distribuido por {{ $brand['legal_name'] }}.</span>
        </div>
    </footer>
</body>
</html>
