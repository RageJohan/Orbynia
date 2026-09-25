@extends('public.layout')
@section('title', 'Términos y condiciones | ORBYNIA')
@section('description', 'Conoce las condiciones de uso del sitio web público de ORBYNIA.')
@section('content')
<section class="subpage-hero"><div class="container narrow"><span class="kicker">INFORMACIÓN LEGAL</span><h1>Términos y condiciones</h1><p>Condiciones de uso del sitio público de ORBYNIA.</p></div></section>
<section class="subpage-body"><div class="container legal-copy">
    <p class="legal-date">Última actualización: 25 de septiembre de 2026.</p>
    <h2>1. Titular del sitio</h2>
    <p>Este sitio pertenece a <strong>{{ $brand['legal_name'] }}</strong>, RUC <strong>{{ $brand['ruc'] }}</strong>, con domicilio en {{ $brand['address'] }}. Puedes contactarnos en <a href="mailto:{{ $brand['contact_email'] }}">{{ $brand['contact_email'] }}</a>.</p>
    <h2>2. Finalidad del sitio</h2>
    <p>La web presenta ORBYNIA, sus funcionalidades y vías de contacto. La información comercial es orientativa. El alcance del servicio, condiciones económicas, niveles de soporte y responsabilidades de cada parte se definen en el contrato que se suscriba con cada empresa cliente.</p>
    <h2>3. Acceso a instancias de clientes</h2>
    <p>Cada empresa cliente tendrá una instancia y una dirección de acceso propias cuando su servicio esté habilitado. El sitio público no solicita credenciales del ERP ni comparte su base de datos.</p>
    <h2>4. Propiedad intelectual</h2>
    <p>La marca ORBYNIA, su identidad visual, textos, interfaz y software pertenecen a sus respectivos titulares. Su publicación en esta web no concede autorización para reproducirlos o explotarlos fuera de los términos contractuales aplicables.</p>
    <h2>5. Disponibilidad y enlaces</h2>
    <p>Trabajamos para mantener la información actualizada y el sitio disponible. Podremos modificar su contenido para reflejar cambios del producto o de los canales de atención. Los enlaces externos se rigen por las condiciones de sus respectivos operadores.</p>
    <h2>6. Privacidad y atención al consumidor</h2>
    <p>El tratamiento de datos del sitio y la app se describe en la <a href="{{ route('privacy') }}">Política de privacidad</a>. Puedes presentar una queja o reclamo mediante el <a href="{{ route('complaints.form') }}">Libro de Reclamaciones</a>.</p>
</div></section>
@endsection