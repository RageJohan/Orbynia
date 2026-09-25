@extends('public.layout')
@section('header-tone', 'dark')

@section('content')
<section class="hero" data-header-theme="dark">
    <div class="hero-stars" aria-hidden="true"></div>
    <div class="hero-orbit hero-orbit-one" aria-hidden="true"></div>
    <div class="hero-orbit hero-orbit-two" aria-hidden="true"></div>
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow"><span class="eyebrow-dot"></span> ERP + movilidad para operaciones reales</span>
            <h1>Cada movimiento de tu empresa, <em>en sincronía.</em></h1>
            <p>Conecta ventas, inventario, reparto y equipos de campo en una plataforma pensada para mantener el control mientras tu negocio avanza.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="mailto:{{ $brand['contact_email'] }}?subject=Quiero%20conocer%20ORBYNIA">Solicitar una demostración <span aria-hidden="true">↗</span></a>
                <a class="text-link" href="#plataforma">Conoce la plataforma <span aria-hidden="true">↓</span></a>
            </div>
            <div class="hero-footnote"><span class="footnote-line"></span> Una sola visión para oficina, almacén y ruta.</div>
        </div>
        <div class="hero-visual" aria-label="Vista conceptual de la plataforma ORBYNIA">
            <div class="visual-halo"></div>
            <div class="dashboard-window">
                <div class="dashboard-top"><span class="dashboard-logo"><img src="{{ asset('images/orbynia/isotipo.png') }}" alt="" width="27" height="27"> Orbynia</span><span class="dashboard-search">Buscar en la plataforma</span><span class="dashboard-avatar">O</span></div>
                <div class="dashboard-body">
                    <div class="dashboard-sidebar" aria-hidden="true"><span class="sidebar-active"></span><span></span><span></span><span></span><span></span></div>
                    <div class="dashboard-content">
                        <div class="dashboard-head"><div><small>Panel general</small><strong>Tu operación, hoy</strong></div><span class="dashboard-live">● En línea</span></div>
                        <div class="dashboard-metrics">
                            <div><small>Pedidos</small><strong>128</strong><span>En seguimiento</span></div>
                            <div><small>Inventario</small><strong>94%</strong><span>Visibilidad de stock</span></div>
                            <div><small>Rutas</small><strong>08</strong><span>En operación</span></div>
                        </div>
                        <div class="dashboard-chart"><div class="chart-title"><strong>Actividad comercial</strong><span>Últimos 7 días</span></div><div class="chart-bars" aria-hidden="true"><i style="--h:42%"></i><i style="--h:58%"></i><i style="--h:49%"></i><i style="--h:70%"></i><i style="--h:61%"></i><i style="--h:86%"></i><i style="--h:76%"></i></div></div>
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
        <div class="process-intro"><span class="kicker">UNA FORMA DE TRABAJAR</span><h2>El mismo ritmo.<br><em>En cada etapa.</em></h2><p>Desde la planificación en oficina hasta la ejecución en campo, cada equipo encuentra la información que necesita para avanzar.</p><a class="text-link" href="mailto:{{ $brand['contact_email'] }}?subject=Consulta%20sobre%20ORBYNIA">Hablemos de tu operación <span aria-hidden="true">↗</span></a></div>
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
        <div class="mobile-copy"><span class="kicker kicker-light">ORBYNIA MÓVIL</span><h2>Tu operación también va contigo.</h2><p>La app acompaña a los equipos que trabajan fuera de la oficina. Cada colaborador accede al entorno de su empresa para consultar y registrar su trabajo durante la jornada.</p><ul><li>Acceso vinculado a la empresa.</li><li>Información comercial y operativa en campo.</li><li>Seguimiento de rutas cuando la función está activa.</li></ul><a class="button button-light" href="mailto:{{ $brand['contact_email'] }}?subject=Conocer%20ORBYNIA%20m%C3%B3vil">Conoce la solución <span aria-hidden="true">↗</span></a></div>
    </div>
</section>

<section class="section company-section" id="empresa">
    <div class="container company-grid"><div><span class="kicker">QUIÉNES SOMOS</span><h2>Tecnología con una identidad clara.</h2></div><div><p>ORBYNIA es una plataforma de gestión empresarial desarrollada y distribuida por <strong>{{ $brand['legal_name'] }}</strong>, empresa peruana con RUC <strong>{{ $brand['ruc'] }}</strong>.</p><p>Cada empresa que contrata el servicio opera en su propia instancia y conserva la identidad de sus procesos y comprobantes.</p></div></div>
</section>

<section class="cta-section" data-header-theme="dark"><div class="container cta-inner"><div><span class="kicker kicker-light">EMPECEMOS A CONVERSAR</span><h2>Una operación mejor conectada empieza aquí.</h2><p>Cuéntanos cómo trabaja tu empresa y conversemos sobre ORBYNIA.</p></div><a class="button button-white" href="mailto:{{ $brand['contact_email'] }}?subject=Quiero%20una%20demostraci%C3%B3n%20de%20ORBYNIA">Escríbenos <span aria-hidden="true">↗</span></a></div></section>
@endsection