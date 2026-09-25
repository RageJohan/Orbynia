@extends('public.layout')
@section('title', 'Libro de Reclamaciones | ORBYNIA')
@section('description', 'Registra una queja o reclamo sobre los servicios ORBYNIA.')
@section('content')
<section class="subpage-hero">
    <div class="container narrow">
        <span class="kicker">ATENCIÓN AL CONSUMIDOR</span>
        <h1>Libro de Reclamaciones</h1>
        <p>Registra una queja o reclamo relacionado con el servicio ORBYNIA. Al enviarlo obtendrás una constancia para guardar o imprimir.</p>
    </div>
</section>
<section class="subpage-body">
    <div class="container form-layout">
        <aside class="form-aside">
            <span class="aside-icon">✳</span>
            <h2>Estamos para escucharte.</h2>
            <p>Proveedor: {{ $brand['legal_name'] }}<br>RUC {{ $brand['ruc'] }}</p>
            <p>{{ $brand['address'] }}</p>
            <p>También puedes escribirnos a <a href="mailto:{{ $brand['contact_email'] }}">{{ $brand['contact_email'] }}</a>.</p>
            <p class="aside-small">Reclamo: disconformidad relacionada con el servicio. Queja: malestar respecto de la atención recibida.</p>
            <p class="aside-small">Responderemos en un plazo máximo de 15 días hábiles, conforme a la normativa de protección al consumidor.</p>
        </aside>
        <form class="public-form" method="post" action="{{ route('complaints.store') }}">
            @csrf
            <h2>Hoja de reclamación</h2>
            <p class="form-intro">Completa los campos marcados con *. Tu registro se guardará con una numeración correlativa.</p>
            @if ($errors->any())
                <div class="form-alert" role="alert">Revisa los campos señalados e intenta nuevamente.</div>
            @endif
            <div class="form-honeypot" aria-hidden="true">
                <label for="website">Deja este campo vacío</label>
                <input id="website" name="website" tabindex="-1" autocomplete="off">
            </div>
            <fieldset>
                <legend>1. Datos del consumidor</legend>
                <div class="form-grid">
                    <div class="field">
                        <label for="first_name">Nombres *</label>
                        <input id="first_name" name="first_name" value="{{ old('first_name') }}" required maxlength="120">
                        @error('first_name')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field">
                        <label for="last_name">Apellidos *</label>
                        <input id="last_name" name="last_name" value="{{ old('last_name') }}" required maxlength="120">
                        @error('last_name')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field">
                        <label for="document_type">Tipo de documento *</label>
                        <select id="document_type" name="document_type" required>
                            <option value="">Selecciona</option>
                            <option value="DNI" @selected(old('document_type')==='DNI')>DNI</option>
                            <option value="CE" @selected(old('document_type')==='CE')>Carné de extranjería</option>
                            <option value="Pasaporte" @selected(old('document_type')==='Pasaporte')>Pasaporte</option>
                            <option value="RUC" @selected(old('document_type')==='RUC')>RUC</option>
                        </select>
                        @error('document_type')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field">
                        <label for="document_number">Número de documento *</label>
                        <input id="document_number" name="document_number" value="{{ old('document_number') }}" required maxlength="24">
                        @error('document_number')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field">
                        <label for="email">Correo electrónico *</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="190">
                        @error('email')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field">
                        <label for="phone">Teléfono *</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" required maxlength="32">
                        @error('phone')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field field-full">
                        <label for="address">Domicilio *</label>
                        <input id="address" name="address" value="{{ old('address') }}" required maxlength="300">
                        @error('address')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field field-full">
                        <input type="hidden" name="is_minor" value="0">
                        <label class="check-label"><input type="checkbox" name="is_minor" value="1" @checked(old('is_minor') == '1')><span>El consumidor es menor de edad</span></label>
                    </div>
                    <div class="field">
                        <label for="guardian_name">Nombre del padre, madre o representante (si corresponde)</label>
                        <input id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" maxlength="180">
                        @error('guardian_name')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field">
                        <label for="guardian_document">Documento del representante (si corresponde)</label>
                        <input id="guardian_document" name="guardian_document" value="{{ old('guardian_document') }}" maxlength="40">
                        @error('guardian_document')<small>{{ $message }}</small>@enderror
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <legend>2. Bien o servicio y solicitud</legend>
                <div class="form-grid">
                    <div class="field">
                        <label for="item_type">Bien o servicio *</label>
                        <select id="item_type" name="item_type" required>
                            <option value="servicio" @selected(old('item_type','servicio')==='servicio')>Servicio</option>
                            <option value="producto" @selected(old('item_type')==='producto')>Producto</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="amount">Monto del bien o servicio (S/) *</label>
                        <input id="amount" type="number" min="0" max="9999999999" step="0.01" name="amount" value="{{ old('amount') }}" required>
                        @error('amount')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field field-full">
                        <label for="item_description">Descripción del bien o servicio *</label>
                        <textarea id="item_description" name="item_description" rows="2" required maxlength="1000">{{ old('item_description') }}</textarea>
                        @error('item_description')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field field-full">
                        <label for="complaint_type">Tipo de registro *</label>
                        <select id="complaint_type" name="complaint_type" required>
                            <option value="">Selecciona</option>
                            <option value="reclamo" @selected(old('complaint_type')==='reclamo')>Reclamo</option>
                            <option value="queja" @selected(old('complaint_type')==='queja')>Queja</option>
                        </select>
                        @error('complaint_type')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field field-full">
                        <label for="description">Detalle de lo sucedido *</label>
                        <textarea id="description" name="description" rows="5" required minlength="10" maxlength="10000">{{ old('description') }}</textarea>
                        @error('description')<small>{{ $message }}</small>@enderror
                    </div>
                    <div class="field field-full">
                        <label for="requested_resolution">¿Qué solución solicitas? *</label>
                        <textarea id="requested_resolution" name="requested_resolution" rows="3" required minlength="5" maxlength="10000">{{ old('requested_resolution') }}</textarea>
                        @error('requested_resolution')<small>{{ $message }}</small>@enderror
                    </div>
                </div>
            </fieldset>
            <label class="check-label">
                <input type="checkbox" name="privacy_accept" value="1" required @checked(old('privacy_accept'))>
                <span>He leído la <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Política de privacidad</a> y autorizo el uso de mis datos para atender esta solicitud. *</span>
            </label>
            @error('privacy_accept')<small class="field-error">{{ $message }}</small>@enderror
            <button class="button button-primary" type="submit">Registrar hoja <span aria-hidden="true">↗</span></button>
            <p class="form-hint">La presentación de una hoja de reclamación no limita otras vías de atención disponibles para el consumidor.</p>
        </form>
    </div>
</section>
@endsection
