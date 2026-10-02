@extends('public.layout')
@section('header-tone', 'dark')

@section('content')
<section class="hero" data-header-theme="dark">
    <div class="hero-stars" aria-hidden="true"></div>
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="hero-comet hero-comet-main" aria-hidden="true">
        <svg class="comet-svg" viewBox="0 0 460 70" preserveAspectRatio="none" fill="none">
            <defs>
                <linearGradient id="cometDustMain" x1="0%" y1="50%" x2="100%" y2="50%">
                    <stop offset="0%" stop-color="#662483" stop-opacity="0" />
                    <stop offset="25%" stop-color="#662483" stop-opacity="0.3" />
                    <stop offset="60%" stop-color="#f39200" stop-opacity="0.65" />
                    <stop offset="88%" stop-color="#ffb84d" stop-opacity="0.9" />
                    <stop offset="100%" stop-color="#ffffff" stop-opacity="0.98" />
                </linearGradient>
                <linearGradient id="cometIonMain" x1="0%" y1="50%" x2="100%" y2="50%">
                    <stop offset="0%" stop-color="#f39200" stop-opacity="0" />
                    <stop offset="40%" stop-color="#f39200" stop-opacity="0.75" />
                    <stop offset="85%" stop-color="#ffe4a0" stop-opacity="0.95" />
                    <stop offset="100%" stop-color="#ffffff" stop-opacity="1" />
                </linearGradient>
                <radialGradient id="cometGlowMain" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stop-color="#ffffff" stop-opacity="1" />
                    <stop offset="22%" stop-color="#ffe082" stop-opacity="0.95" />
                    <stop offset="55%" stop-color="#f39200" stop-opacity="0.7" />
                    <stop offset="85%" stop-color="#662483" stop-opacity="0.3" />
                    <stop offset="100%" stop-color="#662483" stop-opacity="0" />
                </radialGradient>
            </defs>
            <path d="M 0 20 C 160 25 320 31 435 35 C 320 39 160 45 0 50 C 60 39 60 31 0 20 Z" fill="url(#cometDustMain)" />
            <path d="M 50 27 C 180 29 330 33 435 35 C 330 37 180 41 50 43 C 95 38 95 32 50 27 Z" fill="url(#cometDustMain)" opacity="0.85" />
            <path d="M 120 33.5 L 438 35 L 120 36.5 Z" fill="url(#cometIonMain)" />
            <circle cx="435" cy="35" r="18" fill="url(#cometGlowMain)" />
            <ellipse cx="435" cy="35" rx="25" ry="2" fill="#ffffff" opacity="0.95" />
            <ellipse cx="435" cy="35" rx="2" ry="15" fill="#ffffff" opacity="0.9" />
            <circle cx="435" cy="35" r="4.5" fill="#ffffff" />
        </svg>
    </div>
    <div class="hero-comet hero-comet-sub" aria-hidden="true">
        <svg class="comet-svg" viewBox="0 0 360 56" preserveAspectRatio="none" fill="none">
            <defs>
                <linearGradient id="cometDustSub" x1="0%" y1="50%" x2="100%" y2="50%">
                    <stop offset="0%" stop-color="#662483" stop-opacity="0" />
                    <stop offset="35%" stop-color="#8244a8" stop-opacity="0.25" />
                    <stop offset="70%" stop-color="#c97beb" stop-opacity="0.6" />
                    <stop offset="90%" stop-color="#f5e0ff" stop-opacity="0.9" />
                    <stop offset="100%" stop-color="#ffffff" stop-opacity="0.95" />
                </linearGradient>
                <linearGradient id="cometIonSub" x1="0%" y1="50%" x2="100%" y2="50%">
                    <stop offset="0%" stop-color="#c97beb" stop-opacity="0" />
                    <stop offset="50%" stop-color="#c97beb" stop-opacity="0.7" />
                    <stop offset="85%" stop-color="#ffffff" stop-opacity="0.95" />
                    <stop offset="100%" stop-color="#ffffff" stop-opacity="1" />
                </linearGradient>
                <radialGradient id="cometGlowSub" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stop-color="#ffffff" stop-opacity="1" />
                    <stop offset="25%" stop-color="#f3d4ff" stop-opacity="0.9" />
                    <stop offset="60%" stop-color="#c97beb" stop-opacity="0.65" />
                    <stop offset="100%" stop-color="#662483" stop-opacity="0" />
                </radialGradient>
            </defs>
            <path d="M 0 16 C 120 20 250 25 340 28 C 250 31 120 36 0 40 C 45 31 45 25 0 16 Z" fill="url(#cometDustSub)" />
            <path d="M 40 22 C 140 23 260 26 340 28 C 260 30 140 33 40 34 C 75 30 75 26 40 22 Z" fill="url(#cometDustSub)" opacity="0.8" />
            <path d="M 90 26.8 L 342 28 L 90 29.2 Z" fill="url(#cometIonSub)" />
            <circle cx="340" cy="28" r="14" fill="url(#cometGlowSub)" />
            <ellipse cx="340" cy="28" rx="20" ry="1.6" fill="#ffffff" opacity="0.9" />
            <ellipse cx="340" cy="28" rx="1.6" ry="11" fill="#ffffff" opacity="0.85" />
            <circle cx="340" cy="28" r="3.5" fill="#ffffff" />
        </svg>
    </div>
    <div class="hero-orbit hero-orbit-one" aria-hidden="true">
        <span class="orbit-node node-solar"></span>
    </div>
    <div class="hero-orbit hero-orbit-two" aria-hidden="true">
        <span class="orbit-node node-violet"></span>
    </div>
    <div class="hero-orbit hero-orbit-three" aria-hidden="true"></div>
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow anim-hero"><span class="eyebrow-dot"></span> ERP + movilidad para operaciones reales</span>
            <h1 class="anim-hero">Cada movimiento de tu empresa, <em>en sincronía.</em></h1>
            <p class="anim-hero">Conecta ventas, inventario, reparto y equipos de campo en una plataforma pensada para mantener el control mientras tu negocio avanza.</p>
            <div class="hero-actions anim-hero">
                <a class="button button-primary" href="{{ route('contact.form', ['motivo' => 'demo']) }}">Solicitar una demostración <span aria-hidden="true">↗</span></a>
                <a class="text-link" href="#plataforma">Conoce la plataforma <span aria-hidden="true">↓</span></a>
            </div>
            <div class="hero-footnote anim-hero"><span class="footnote-line"></span> ¿Ya tienes una empresa en ORBYNIA? <a href="{{ route('access.form') }}">Ingresa aquí</a>.</div>
        </div>
        <div class="hero-visual anim-hero" aria-label="Vista conceptual de la plataforma ORBYNIA">
            <div class="visual-halo"></div>
            <div class="dashboard-window">
                <div class="dashboard-top"><span class="dashboard-logo"><img src="{{ asset('images/orbynia/isotipo.png') }}" alt="" width="27" height="27"> Orbynia</span><span class="dashboard-search">Buscar en la plataforma</span><span class="dashboard-avatar">O</span></div>
                <div class="dashboard-body">
                    <div class="dashboard-sidebar" aria-hidden="true"><span class="sidebar-active"></span><span></span><span></span><span></span><span></span></div>
                    <div class="dashboard-content">
                        <div class="dashboard-head"><div><small>Panel general</small><strong>Tu operación, hoy</strong></div><span class="dashboard-live"><span class="live-pulse"></span>En línea</span></div>
                        <div class="dashboard-metrics">
                            <div><small>Pedidos</small><strong>128</strong><span>En seguimiento</span></div>
                            <div><small>Inventario</small><strong>94%</strong><span>Visibilidad de stock</span></div>
                            <div><small>Rutas</small><strong>08</strong><span>En operación</span></div>
                        </div>
                        <div class="dashboard-chart"><div class="chart-title"><strong>Actividad comercial</strong><span>Últimos 7 días</span></div><div class="chart-bars" aria-hidden="true"><i style="--h:42%; --bar-delay:0.05s"></i><i style="--h:58%; --bar-delay:0.12s"></i><i style="--h:49%; --bar-delay:0.19s"></i><i style="--h:70%; --bar-delay:0.26s"></i><i style="--h:61%; --bar-delay:0.33s"></i><i style="--h:86%; --bar-delay:0.40s"></i><i style="--h:76%; --bar-delay:0.47s"></i></div></div>
                    </div>
                </div>
            </div>
            <div class="floating-card"><span class="floating-icon">↗</span><span><strong>Equipo conectado</strong><small>Oficina y campo en una vista</small></span></div>
            <span class="visual-caption">Vista conceptual. Datos ilustrativos.</span>
        </div>
    </div>
</section>

<section class="trust-strip" aria-label="Áreas de operación" data-header-theme="dark">
    <div class="container trust-inner">
        <div class="trust-heading">
            <span class="kicker kicker-light">UNA OPERACIÓN CONECTADA</span>
            <p>De la primera venta a la última entrega, <strong>todo sigue el mismo pulso.</strong></p>
        </div>
        <div class="trust-flow">
            <div class="flow-step"><span class="flow-number">01</span><strong>Ventas</strong><small>El punto de partida</small></div>
            <div class="flow-step"><span class="flow-number">02</span><strong>Inventario</strong><small>Control en cada movimiento</small></div>
            <div class="flow-step"><span class="flow-number">03</span><strong>Reparto</strong><small>Avance en campo</small></div>
            <div class="flow-step"><span class="flow-number">04</span><strong>Gestión</strong><small>Decisiones con contexto</small></div>
        </div>
    </div>
</section>

<section class="section platform-section" id="plataforma">
    <div class="container">
        <div class="section-heading"><div><span class="kicker">LA PLATAFORMA</span><h2>Menos sistemas aislados.<br><em>Más control para crecer.</em></h2></div><p>ORBYNIA reúne los procesos diarios de una operación comercial en un entorno que conecta a quienes planifican, venden y entregan.</p></div>
        <div class="feature-grid">
            <article class="feature-card"><span class="feature-number">01 / OPERACIÓN</span><div class="feature-symbol symbol-inventory" aria-hidden="true"><span></span><span></span><span></span></div><h3>Inventario que acompaña cada decisión.</h3><p>Organiza productos, existencias, lotes y movimientos para trabajar con información consistente desde el almacén hasta la venta.</p><span class="feature-arrow" aria-hidden="true">↗</span></article>
            <article class="feature-card"><span class="feature-number">02 / COMERCIAL</span><div class="feature-symbol symbol-sales" aria-hidden="true"><span></span><span></span><span></span></div><h3>Ventas y pedidos en movimiento.</h3><p>Da seguimiento a clientes, precios, pedidos y cobranzas con una operación conectada.</p><span class="feature-arrow" aria-hidden="true">↗</span></article>
            <article class="feature-card"><span class="feature-number">03 / DISTRIBUCIÓN</span><div class="feature-symbol symbol-route" aria-hidden="true"><span></span><span></span><span></span></div><h3>Rutas con mejor visibilidad.</h3><p>Coordina reparto y equipos de campo, con seguimiento de las actividades de cada jornada.</p><span class="feature-arrow" aria-hidden="true">↗</span></article>
            <article class="feature-card"><span class="feature-number">04 / GESTIÓN</span><div class="feature-symbol symbol-report" aria-hidden="true"><span></span><span></span><span></span><span></span></div><h3>Información útil para quienes toman decisiones.</h3><p>Consulta reportes operativos y mantén una visión más clara de lo que ocurre en cada área.</p><span class="feature-arrow" aria-hidden="true">↗</span></article>
        </div>
    </div>
</section>

<section class="section process-section" id="como-funciona">
    <div class="container process-grid">
        <div class="process-intro"><span class="kicker">UNA FORMA DE TRABAJAR</span><h2>El mismo ritmo.<br><em>En cada etapa.</em></h2><p>Desde la planificación en oficina hasta la ejecución en campo, cada equipo encuentra la información que necesita para avanzar.</p><a class="text-link" href="{{ route('contact.form', ['motivo' => 'operacion']) }}">Hablemos de tu operación <span aria-hidden="true">↗</span></a></div>
        <div class="process-list">
            <article><span>01</span><div><h3>Planifica</h3><p>Ordena productos, clientes, precios y tareas desde tu ERP.</p></div></article>
            <article><span>02</span><div><h3>Ejecuta</h3><p>Tu equipo trabaja con la información de su empresa desde la web y la app móvil.</p></div></article>
            <article><span>03</span><div><h3>Supervisa</h3><p>Revisa avances, rutas y resultados para ajustar a tiempo.</p></div></article>
        </div>
    </div>
</section>

<section class="section mobile-section" id="movilidad">
    <div class="container mobile-grid">
        <div class="mobile-visual"><div class="phone-shadow"></div><div class="phone-frame"><div class="phone-top"><span></span><img src="{{ asset('images/orbynia/isotipo.png') }}" alt="" width="30" height="30"></div><div class="phone-screen"><small>Hola, equipo</small><strong>Tu jornada<br>en movimiento.</strong><div class="phone-route"><span class="route-dot route-start"></span><span class="route-path"></span><span class="route-dot route-end"></span></div><div class="phone-task"><span>01</span><div><b>Clientes y pedidos</b><small>Información para tu ruta</small></div><span>↗</span></div><div class="phone-task"><span>02</span><div><b>Entregas</b><small>Avance de la jornada</small></div><span>↗</span></div></div></div><div class="mobile-chip">ORBYNIA <span>móvil</span></div></div>
        <div class="mobile-copy"><span class="kicker kicker-light">ORBYNIA MÓVIL</span><h2>Tu operación también va contigo.</h2><p>La app acompaña a los equipos que trabajan fuera de la oficina. Cada colaborador accede al entorno de su empresa para consultar y registrar su trabajo durante la jornada.</p><ul><li>Acceso vinculado a la empresa.</li><li>Información comercial y operativa en campo.</li><li>Seguimiento de rutas cuando la función está activa.</li></ul><a class="button button-light" href="{{ route('contact.form', ['motivo' => 'movil']) }}">Conoce la solución <span aria-hidden="true">↗</span></a></div>
    </div>
</section>

<section class="section plans-section" id="planes">
    <div class="container">
        <div class="section-heading"><div><span class="kicker">PLANES ORBYNIA</span><h2>Un punto de partida<br><em>para cada operación.</em></h2></div><p>Tres opciones referenciales. Todas incluyen acceso al ERP y a la app móvil. Módulos, duración y precio se definen con tu empresa antes de la activación.</p></div>
        <div class="plans-grid">
            <article class="plan-card"><span>01 / INICIO</span><h3>Inicio</h3><p>Para comenzar a ordenar los procesos principales de tu empresa.</p><a href="{{ route('evaluation.form', ['plan' => 'inicio']) }}">Solicitar evaluación <span aria-hidden="true">↗</span></a></article>
            <article class="plan-card plan-card-featured"><span>02 / CRECIMIENTO</span><h3>Crecimiento</h3><p>Para conectar más áreas y ampliar la visibilidad de la operación.</p><a href="{{ route('evaluation.form', ['plan' => 'crecimiento']) }}">Solicitar evaluación <span aria-hidden="true">↗</span></a></article>
            <article class="plan-card"><span>03 / INTEGRAL</span><h3>Integral</h3><p>Para conversar sobre una operación con necesidades más amplias.</p><a href="{{ route('evaluation.form', ['plan' => 'integral']) }}">Solicitar evaluación <span aria-hidden="true">↗</span></a></article>
        </div>
        <p class="plans-note">Presentación referencial: la selección expresa interés y no constituye contratación ni cobro.</p>
    </div>
</section>
<section class="section company-section" id="empresa">
    <div class="container company-grid"><div><span class="kicker">QUIÉNES SOMOS</span><h2>Tecnología con una identidad clara.</h2></div><div><p>ORBYNIA es una plataforma de gestión empresarial desarrollada y distribuida por <strong>{{ $brand['legal_name'] }}</strong>, empresa peruana con RUC <strong>{{ $brand['ruc'] }}</strong>.</p><p>Cada empresa que contrata el servicio opera en su propia instancia y conserva la identidad de sus procesos y comprobantes.</p></div></div>
</section>

<section class="cta-section" data-header-theme="dark"><div class="container cta-inner"><div><span class="kicker kicker-light">EMPECEMOS A CONVERSAR</span><h2>Una operación mejor conectada empieza aquí.</h2><p>Cuéntanos cómo trabaja tu empresa y conversemos sobre ORBYNIA.</p></div><a class="button button-white" href="{{ route('contact.form', ['motivo' => 'contacto']) }}">Escríbenos <span aria-hidden="true">↗</span></a></div></section>
@endsection
