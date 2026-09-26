@extends('public.layout')
@section('title', 'Política de privacidad | ORBYNIA')
@section('description', 'Información sobre el tratamiento de datos personales en el sitio y la aplicación ORBYNIA, incluida la ubicación.')
@section('content')
<section class="subpage-hero"><div class="container narrow"><span class="kicker">TUS DATOS</span><h1>Política de privacidad</h1><p>Información clara sobre los datos usados por la web, el ERP y la app móvil ORBYNIA.</p></div></section>
<section class="subpage-body"><div class="container legal-copy">
    <p class="legal-date">Última actualización: 25 de septiembre de 2026.</p>
    <h2>1. Quién opera ORBYNIA</h2>
    <p><strong>{{ $brand['legal_name'] }}</strong>, RUC <strong>{{ $brand['ruc'] }}</strong>, desarrolla y distribuye ORBYNIA. Su domicilio es {{ $brand['address'] }} y su contacto de privacidad es <a href="mailto:{{ $brand['contact_email'] }}">{{ $brand['contact_email'] }}</a>. Cada empresa cliente administra su propia instancia y determina qué colaboradores utilizan sus procesos empresariales.</p>
    <h2>2. Datos que se tratan</h2>
    <p>Al contactar a ORBYNIA, solicitar una evaluación o usar el Libro de Reclamaciones podemos recibir nombre, documento, correo, dirección, teléfono, nombre, domicilio, teléfono y correo del padre o representante cuando un reclamo corresponda a un menor y el contenido de tu solicitud. Las instancias ERP pueden tratar datos de usuarios, clientes comerciales, pedidos, inventarios, entregas, cobranzas y comprobantes, según los módulos contratados por cada empresa.</p>
    <p>La app móvil puede transmitir a la instancia de tu empresa tu identificador de usuario y coordenadas de ubicación durante el seguimiento de una ruta o jornada cuando el servicio de ubicación está activo. El servicio Android puede continuar durante esa actividad aunque la app quede minimizada, con una notificación persistente. El código actual solicita actualizaciones aproximadamente cada 20 segundos; la frecuencia efectiva depende del dispositivo y del sistema.</p>
    <h2>3. Para qué se usan</h2>
    <p>Usamos los datos del sitio para responder consultas, verificar el correo de quien solicita una evaluación, revisar su alta, registrar y atender reclamaciones y gestionar solicitudes sobre cuentas. La solicitud de evaluación puede incluir nombre de empresa, plan de interés, subdominio propuesto y datos de su responsable. Estos datos se conservan en la base central de MINKA, separada de las bases de datos de las instancias de clientes. Los datos del ERP y la app permiten ejecutar las operaciones contratadas, autenticar usuarios, registrar actividades de campo y consultar el avance de rutas cuando la empresa habilita esa función. La ubicación no se utiliza para publicidad.</p>
    <h2>4. Acceso y proveedores</h2>
    <p>El personal autorizado de la empresa cliente puede consultar los datos de su instancia según sus permisos. MINKA 360 S.A.C. puede acceder a información necesaria para operar y prestar soporte al servicio. Cuando una empresa utiliza emisión electrónica, los datos necesarios de sus comprobantes se transmiten al proveedor tecnológico contratado, como NubeFact, para la emisión en nombre de esa empresa. La empresa cliente conserva su condición de emisora ante SUNAT.</p>
    <h2>5. Conservación y seguridad</h2>
    <p>Conservamos los datos durante el tiempo necesario para prestar el servicio y cumplir obligaciones aplicables. Algunos registros comerciales o tributarios deben mantenerse durante los plazos legales correspondientes. Aplicamos controles de acceso y protección técnica; el detalle de conservación de cada instancia depende también del contrato y de las obligaciones de la empresa cliente.</p>
    <h2>6. Solicitudes sobre tus datos</h2>
    <p>Para consultas sobre acceso, rectificación o eliminación de datos, escribe a <a href="mailto:{{ $brand['contact_email'] }}">{{ $brand['contact_email'] }}</a>. Si eres colaborador o cliente de una empresa que utiliza ORBYNIA, identifica esa empresa para coordinar la atención con quien administra su instancia. Antes de actuar verificaremos la titularidad y comunicaremos cualquier obligación legal de conservación.</p>
    <div class="legal-callout">
        <h3>Eliminar una cuenta de ORBYNIA móvil</h3>
        <p>Puedes iniciar la solicitud aunque ya no tengas acceso a la app. Indica tu empresa y la cuenta para que podamos coordinar la atención con su instancia, previa verificación de identidad.</p>
        <a class="button button-primary" href="{{ route('deletion.form') }}">Solicitar eliminación de cuenta <span aria-hidden="true">↗</span></a>
    </div>
    <h2>7. Sitio web y cookies</h2>
    <p>El sitio utiliza los recursos de sesión necesarios para proteger y procesar los formularios. Esta primera versión no integra publicidad comportamental ni herramientas de analítica. Si se añaden servicios externos o nuevos tratamientos, actualizaremos esta política.</p>
    <h2>8. Cambios y contacto</h2>
    <p>Publicaremos aquí las actualizaciones de esta política. Para cualquier pregunta sobre privacidad, contáctanos en <a href="mailto:{{ $brand['contact_email'] }}">{{ $brand['contact_email'] }}</a>.</p>
</div></section>
@endsection