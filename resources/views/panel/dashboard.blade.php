<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Panel de gestión | ORBYNIA</title>
    @vite(['resources/css/app.css'])
    @include('partials.favicon')
</head>
<body class="panel-body panel-dashboard-body">
<header class="panel-header">
    <strong>ORBYNIA <span aria-hidden="true">·</span> Panel de MINKA</strong>
    <form method="post" action="{{ route('minka.logout') }}">@csrf<button type="submit">Salir <span aria-hidden="true">↗</span></button></form>
</header>
<main class="panel-main panel-dashboard-main">
    <section class="panel-dashboard-intro">
        <div>
            <p class="panel-eyebrow"><span class="panel-status-dot"></span> CENTRO DE OPERACIONES</p>
            <h1>Panel de gestión</h1>
            <p>Selecciona un área para continuar con las solicitudes de ORBYNIA.</p>
        </div>
        <div class="panel-dashboard-mark">
            <img src="{{ asset('images/orbynia/logo-color.png') }}" alt="ORBYNIA" width="210" height="60">
        </div>
    </section>

    @if(session('panel_success'))<div class="form-success" role="status">{{ session('panel_success') }}</div>@endif
    @if(session('panel_warning'))<div class="form-alert" role="status">{{ session('panel_warning') }}</div>@endif

    <section class="panel-module-grid" aria-label="Áreas de gestión">
        <article class="panel-module-card panel-module-card-commercial">
            <div class="panel-module-card-top">
                <div class="panel-module-icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48" fill="none"><path d="M8 17.5h32v21H8zM5 17.5l4-9h30l4 9M17 17.5v6h14v-6M14 29h8m4 0h8M14 34h20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 8.5V5h22v3.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                </div>
                <span class="panel-module-index">01</span>
            </div>
            <div class="panel-module-content">
                <span class="panel-module-label">CLIENTES INTERESADOS</span>
                <h2>Gestión comercial</h2>
                <p>Contactos, demostraciones, solicitudes de servicio y sus seguimientos.</p>
            </div>
            <div class="panel-module-data">
                <div class="panel-module-count"><strong>{{ $commercialCount }}</strong><span>registros recibidos</span></div>
                <div class="panel-follow-up"><span class="panel-follow-up-icon" aria-hidden="true">↗</span><span><strong>{{ $dueFollowUpsCount }}</strong> seguimientos próximos o vencidos</span></div>
            </div>
            <a class="panel-module-button" href="{{ route('minka.commercial') }}">
                <span>Abrir gestión comercial</span><span class="panel-button-arrow" aria-hidden="true">→</span>
            </a>
        </article>

        <article class="panel-module-card panel-module-card-support">
            <div class="panel-module-card-top">
                <div class="panel-module-icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48" fill="none"><path d="M13 6.5h16l8 8v27H13z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><path d="M29 7v9h8M19 25h12m-12 6h12m-12 6h7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><path d="m8 17 3 3-3 3" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <span class="panel-module-index">02</span>
            </div>
            <div class="panel-module-content">
                <span class="panel-module-label">ATENCIÓN Y PRIVACIDAD</span>
                <h2>Casos recibidos</h2>
                <p>Reclamaciones y solicitudes relacionadas con eliminación de cuenta.</p>
            </div>
            <div class="panel-module-data">
                <div class="panel-module-count"><strong>{{ $legalCount }}</strong><span>casos registrados</span></div>
                <div class="panel-support-note"><span class="panel-support-note-icon" aria-hidden="true">✓</span> Seguimiento desde una sola lista</div>
            </div>
            <a class="panel-module-button panel-module-button-support" href="{{ route('minka.legal') }}">
                <span>Abrir atención de casos</span><span class="panel-button-arrow" aria-hidden="true">→</span>
            </a>
        </article>
    </section>
    <p class="panel-dashboard-footnote"><span>ORBYNIA</span> · Plataforma operada por MINKA 360 S.A.C.</p>
</main>
</body>
</html>