<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $type === 'complaint' ? 'Reclamación' : 'Solicitud de eliminación' }} {{ $case->reference }} | Panel de MINKA</title>
    @vite(['resources/css/app.css'])
</head>
<body class="panel-body">
@php
    $isComplaint = $type === 'complaint';
    $responseValue = $isComplaint ? ($case->provider_actions ?? '') : ($case->response ?? '');
@endphp
<header class="panel-header">
    <strong>ORBYNIA · Panel de MINKA</strong>
    <a href="{{ $backRoute }}">Volver al panel</a>
</header>
<main class="panel-main panel-detail">
    <p class="panel-eyebrow">{{ $isComplaint ? 'LIBRO DE RECLAMACIONES' : 'PRIVACIDAD Y CONTROL' }}</p>
    <h1>{{ $isComplaint ? 'Reclamación' : 'Solicitud de eliminación' }} {{ $case->reference }}</h1>
    <p>Registrada el {{ \Illuminate\Support\Carbon::parse($case->created_at)->format('d/m/Y H:i') }} · Estado: <strong>{{ $statuses[$case->status] ?? $case->status }}</strong></p>

    @if(session('panel_success'))<div class="form-success" role="status">{{ session('panel_success') }}</div>@endif
    @if(session('panel_warning'))<div class="form-alert" role="status">{{ session('panel_warning') }}</div>@endif
    @if($errors->any())<div class="form-alert" role="alert">{{ $errors->first() }}</div>@endif

    <section class="panel-case-grid">
        <article class="panel-card panel-case-summary">
            <h2>{{ $isComplaint ? 'Datos del consumidor y reclamación' : 'Datos de la solicitud' }}</h2>
            @if($isComplaint)
                <dl class="panel-data-list">
                    <div><dt>Consumidor</dt><dd>{{ $case->first_name }} {{ $case->last_name }}</dd></div>
                    <div><dt>Documento</dt><dd>{{ $case->document_type }} {{ $case->document_number }}</dd></div>
                    <div><dt>Correo y teléfono</dt><dd>{{ $case->email }} · {{ $case->phone }}</dd></div>
                    <div><dt>Domicilio</dt><dd>{{ $case->address }}</dd></div>
                    @if($case->is_minor)
                        <div><dt>Representante</dt><dd>{{ $case->guardian_name }} · {{ $case->guardian_email }} · {{ $case->guardian_phone }}</dd></div>
                        <div><dt>Domicilio del representante</dt><dd>{{ $case->guardian_address }}</dd></div>
                    @endif
                    <div><dt>Tipo</dt><dd>{{ ucfirst($case->complaint_type) }}</dd></div>
                    <div><dt>Servicio</dt><dd>{{ $case->item_description }}</dd></div>
                    <div><dt>Monto</dt><dd>{{ $case->amount !== null ? 'S/ '.number_format((float) $case->amount, 2, ',', ' ') : 'No indicado / no aplica' }}</dd></div>
                    <div><dt>Detalle</dt><dd>{{ $case->description }}</dd></div>
                    <div><dt>Solución solicitada</dt><dd>{{ $case->requested_resolution }}</dd></div>
                </dl>
            @else
                <dl class="panel-data-list">
                    <div><dt>Solicitante</dt><dd>{{ $case->full_name }}</dd></div>
                    <div><dt>Correo</dt><dd>{{ $case->email }}</dd></div>
                    <div><dt>Empresa</dt><dd>{{ $case->company }}</dd></div>
                    <div><dt>Usuario o correo de la cuenta</dt><dd>{{ $case->account_identifier }}</dd></div>
                    <div><dt>Información adicional</dt><dd>{{ $case->details ?: 'Sin información adicional.' }}</dd></div>
                </dl>
            @endif
        </article>

        <section class="panel-card">
            <h2>Gestión del caso</h2>
            <form method="post" action="{{ $updateRoute }}">
                @csrf
                <label for="case-status">Estado</label>
                <select id="case-status" name="status" required>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $case->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <label for="responsible-operator">Responsable</label>
                <select id="responsible-operator" name="responsible_operator_id">
                    <option value="">Sin asignar</option>
                    @foreach($operators as $operator)
                        <option value="{{ $operator->id }}" @selected((string) old('responsible_operator_id', $case->responsible_operator_id) === (string) $operator->id)>{{ $operator->name }} · {{ $operator->email }}</option>
                    @endforeach
                </select>

                <label for="internal-notes">Notas internas</label>
                <textarea id="internal-notes" name="internal_notes" rows="5" maxlength="10000">{{ old('internal_notes', $case->internal_notes) }}</textarea>
                <p class="field-hint">Estas notas solo se muestran en el Panel de MINKA.</p>

                <label for="case-response">Respuesta al solicitante</label>
                <textarea id="case-response" name="response" rows="7" maxlength="10000">{{ old('response', $responseValue) }}</textarea>
                <p class="field-hint">@if($isComplaint)La respuesta también aparecerá en la constancia de reclamación.@elseLa respuesta se guarda en el expediente de eliminación.@endif</p>
                @if($case->response_sent_at)
                    <p class="field-hint">Último envío registrado: {{ \Illuminate\Support\Carbon::parse($case->response_sent_at)->format('d/m/Y H:i') }}.</p>
                @endif

                <label class="panel-check"><input type="checkbox" name="send_response" value="1" @checked(old('send_response'))>Enviar esta respuesta por correo al solicitante al guardar</label>
                <button class="button button-primary" type="submit">Guardar gestión</button>
            </form>
        </section>
    </section>

    <section class="panel-card panel-history">
        <h2>Historial de gestión</h2>
        @if($events->isEmpty())
            <p>Aún no hay cambios registrados desde el panel.</p>
        @else
            <ul>
                @foreach($events as $event)
                    <li><strong>{{ \Illuminate\Support\Carbon::parse($event->created_at)->format('d/m/Y H:i') }}</strong> · {{ $event->operator_name ?: 'Operador eliminado' }} · {{ $event->details }}</li>
                @endforeach
            </ul>
        @endif
    </section>
</main>
</body>
</html>
