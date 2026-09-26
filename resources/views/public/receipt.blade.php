@extends('public.layout')
@section('title', 'Constancia de reclamación | ORBYNIA')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="subpage-hero print-hidden">
    <div class="container narrow">
        <span class="kicker">LIBRO DE RECLAMACIONES</span>
        <h1>Tu registro fue recibido.</h1>
        <p>Guarda o imprime esta constancia para tus archivos.</p>
    </div>
</section>
<section class="subpage-body">
    <div class="container narrow">
        <article class="receipt-card">
            <div class="receipt-heading">
                <img src="{{ asset('images/orbynia/logo-color.png') }}" alt="ORBYNIA" width="180">
                <span>Hoja de reclamación</span>
            </div>
            <p class="receipt-reference">{{ $complaint->reference }}</p>
            <dl>
                <div><dt>Proveedor</dt><dd>{{ $brand['legal_name'] }} · RUC {{ $brand['ruc'] }}</dd></div>
                <div><dt>Domicilio del proveedor</dt><dd>{{ $brand['address'] }}</dd></div>
                <div><dt>Fecha de registro</dt><dd>{{ \Illuminate\Support\Carbon::parse($complaint->created_at)->format('d/m/Y H:i') }}</dd></div>
                <div><dt>Consumidor</dt><dd>{{ $complaint->first_name }} {{ $complaint->last_name }}</dd></div>
                <div><dt>Documento</dt><dd>{{ $complaint->document_type }} {{ $complaint->document_number }}</dd></div>
                @if($complaint->is_minor)
                    <div><dt>Representante</dt><dd>{{ $complaint->guardian_name }}</dd></div>
                    @if($complaint->guardian_address)<div><dt>Domicilio del representante</dt><dd>{{ $complaint->guardian_address }}</dd></div>@endif
                    @if($complaint->guardian_phone)<div><dt>Teléfono del representante</dt><dd>{{ $complaint->guardian_phone }}</dd></div>@endif
                    @if($complaint->guardian_email)<div><dt>Correo del representante</dt><dd>{{ $complaint->guardian_email }}</dd></div>@endif
                @endif
                <div><dt>Domicilio</dt><dd>{{ $complaint->address }}</dd></div>
                <div><dt>Correo</dt><dd>{{ $complaint->email }}</dd></div>
                <div><dt>Teléfono</dt><dd>{{ $complaint->phone }}</dd></div>
                <div><dt>Tipo de registro</dt><dd>{{ ucfirst($complaint->complaint_type) }}</dd></div>
                <div><dt>Producto o servicio reclamado</dt><dd>{{ $complaint->item_description }}</dd></div>
                <div><dt>Monto del producto o servicio</dt><dd>{{ $complaint->amount !== null ? 'S/ '.number_format((float) $complaint->amount, 2, ',', ' ') : 'No indicado / no aplica' }}</dd></div>
                <div><dt>Detalle</dt><dd>{{ $complaint->description }}</dd></div>
                <div><dt>Solicitud</dt><dd>{{ $complaint->requested_resolution }}</dd></div>
                <div><dt>Acciones del proveedor</dt><dd>{{ $complaint->provider_actions ?: 'Pendiente de atención y respuesta al consumidor.' }}</dd></div>
            </dl>
            <p>Tu caso ha quedado registrado. ORBYNIA se comunicará contigo mediante los datos proporcionados para su atención.</p>
        </article>
        <div class="receipt-actions print-hidden">
            <button type="button" class="button button-primary" onclick="window.print()">Imprimir constancia</button>
            <a class="text-link" href="{{ route('home') }}">Volver al inicio <span aria-hidden="true">↗</span></a>
        </div>
    </div>
</section>
@endsection
