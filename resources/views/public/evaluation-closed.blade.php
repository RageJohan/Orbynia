@extends('public.layout')
@section('title', 'Solicitud cerrada | ORBYNIA')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="subpage-hero">
    <div class="container narrow">
        <span class="kicker">SOLICITUD CERRADA</span>
        <h1>Esta solicitud ya no está vigente.</h1>
        <p>Si quieres volver a evaluar ORBYNIA para tu empresa, envía una nueva solicitud y elige un subdominio disponible.</p>
        <a class="button button-primary" href="{{ route('evaluation.form') }}">Enviar otra solicitud</a>
    </div>
</section>
@endsection
