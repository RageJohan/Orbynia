<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Seguimiento de {{ $companyName }} | Panel de MINKA</title>
    @vite(['resources/css/app.css'])
    @include('partials.favicon')
</head>
<body class="panel-body">
<header class="panel-header">
    <strong>ORBYNIA · Panel de MINKA</strong>
    <a href="{{ $backUrl }}">Volver</a>
</header>
<main class="panel-main panel-detail">
    <p class="panel-eyebrow">SEGUIMIENTO COMERCIAL · {{ $type === 'contacto' ? 'CONTACTO' : 'SOLICITUD DE EVALUACIÓN' }}</p>
    <h1>{{ $companyName }}</h1>
    <p><strong>Contacto:</strong> {{ $contactName }} · {{ $record->email }} · {{ $record->phone }}</p>
    @if($type === 'contacto')
        <p><strong>Motivo:</strong> {{ $record->source }} · <strong>Mensaje:</strong> {{ $record->message ?: 'Sin mensaje.' }}</p>
    @else
        <p><strong>Plan de interés:</strong> {{ ucfirst($record->plan_interest) }} · <strong>Subdominio:</strong> {{ $record->requested_slug }}.orbynia.com · <strong>Estado del alta:</strong> {{ $record->status }}</p>
        <p><a href="{{ route('minka.application', $record->id) }}">Ver la solicitud y su aprobación</a></p>
    @endif

    @if(session('panel_success'))<div class="form-success" role="status">{{ session('panel_success') }}</div>@endif
    @if($errors->any())<div class="form-alert" role="alert"><p>Revisa los datos:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="panel-sales-grid">
        <article class="panel-card">
            <h2>Próximo paso</h2>
            <form method="post" action="{{ route('minka.sales.update', [$type, $record->id]) }}">
                @csrf
                <label for="sales-stage">Etapa comercial</label>
                <select id="sales-stage" name="stage" required>
                    @foreach($stages as $value => $label)
                        <option value="{{ $value }}" @selected(old('stage', $stage) === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <label for="sales-owner">Responsable en MINKA</label>
                <select id="sales-owner" name="sales_owner_id">
                    <option value="">Sin asignar</option>
                    @foreach($operators as $operator)
                        <option value="{{ $operator->id }}" @selected((string) old('sales_owner_id', $record->sales_owner_id) === (string) $operator->id)>{{ $operator->name }}</option>
                    @endforeach
                </select>

                <label for="next-follow-up">Próximo seguimiento</label>
                <input id="next-follow-up" type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', $record->next_follow_up_at ? \Illuminate\Support\Carbon::parse($record->next_follow_up_at)->format('Y-m-d\TH:i') : '') }}">
                <p class="field-hint">La fecha aparecerá en la lista de seguimientos del panel. Déjala vacía para quitarla.</p>
                <button class="button button-primary" type="submit">Guardar próximo paso</button>
            </form>
        </article>

        <article class="panel-card">
            <h2>Registrar contacto o avance</h2>
            <form method="post" action="{{ route('minka.sales.activity', [$type, $record->id]) }}">
                @csrf
                <label for="activity-type">Tipo de actividad</label>
                <select id="activity-type" name="activity_type" required>
                    @foreach($activityTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <label for="activity-at">Fecha de la actividad</label>
                <input id="activity-at" type="datetime-local" name="occurred_at" value="{{ now()->format('Y-m-d\TH:i') }}">
                <label for="activity-description">Qué ocurrió</label>
                <textarea id="activity-description" name="description" rows="5" required maxlength="5000" placeholder="Ej.: llamé al gerente, pidió una demostración para su equipo.">{{ old('description') }}</textarea>
                <label for="activity-next">Próximo seguimiento (opcional)</label>
                <input id="activity-next" type="datetime-local" name="next_follow_up_at">
                <button class="button button-primary" type="submit">Registrar actividad</button>
            </form>
        </article>
    </section>

    <section class="panel-card">
        <h2>Registrar propuesta</h2>
        <p>Este registro es interno. MINKA comparte la propuesta con el cliente por el canal que acuerden.</p>
        <form method="post" action="{{ route('minka.sales.proposal', [$type, $record->id]) }}">
            @csrf
            <label for="proposal-title">Título o versión</label>
            <input id="proposal-title" name="title" value="{{ old('title') }}" required maxlength="160" placeholder="Ej.: Propuesta ORBYNIA para {{ $companyName }}">
            <div class="panel-form-row">
                <div><label for="proposal-amount">Monto (S/)</label><input id="proposal-amount" type="number" name="amount" value="{{ old('amount') }}" min="0" max="9999999999.99" step="0.01" required></div>
                <div><label for="proposal-period">Periodo</label><select id="proposal-period" name="billing_period" required><option value="monthly">Mensual</option><option value="annual">Anual</option><option value="one_time">Pago único</option></select></div>
            </div>
            <label for="proposal-modules">Módulos incluidos</label>
            <textarea id="proposal-modules" name="modules" rows="3" maxlength="5000" placeholder="Ej.: ventas, inventario y reparto">{{ old('modules') }}</textarea>
            <label for="proposal-scope">Alcance y condiciones resumidas</label>
            <textarea id="proposal-scope" name="scope" rows="4" maxlength="10000">{{ old('scope') }}</textarea>
            <div class="panel-form-row">
                <div><label for="proposal-status">Estado inicial</label><select id="proposal-status" name="status"><option value="draft">Borrador</option><option value="sent">Ya enviada al cliente</option></select></div>
                <div><label for="proposal-sent-at">Fecha de envío (si aplica)</label><input id="proposal-sent-at" type="datetime-local" name="sent_at"></div>
                <div><label for="proposal-valid-until">Válida hasta (opcional)</label><input id="proposal-valid-until" type="date" name="valid_until"></div>
            </div>
            <button class="button button-primary" type="submit">Guardar propuesta</button>
        </form>
    </section>

    <section class="panel-card">
        <h2>Propuestas registradas</h2>
        @forelse($proposals as $proposal)
            <article class="panel-sales-entry">
                <h3>{{ $proposal->title }} <small>#{{ $proposal->id }}</small></h3>
                <p><strong>S/ {{ number_format((float) $proposal->amount, 2, ',', ' ') }}</strong> · {{ ['monthly' => 'mensual', 'annual' => 'anual', 'one_time' => 'pago único'][$proposal->billing_period] ?? $proposal->billing_period }} · {{ $proposalStatuses[$proposal->status] ?? $proposal->status }}</p>
                <p>Registró: {{ $proposal->operator_name ?: 'Operador anterior' }} · {{ $proposal->created_at }} @if($proposal->sent_at) · Enviada: {{ $proposal->sent_at }} @endif @if($proposal->valid_until) · Válida hasta: {{ $proposal->valid_until }} @endif</p>
                @if($proposal->modules)<p><strong>Módulos:</strong> {{ $proposal->modules }}</p>@endif
                @if($proposal->scope)<p><strong>Alcance:</strong> {{ $proposal->scope }}</p>@endif
                <form class="panel-inline-form" method="post" action="{{ route('minka.sales.proposal.update', [$type, $record->id, $proposal->id]) }}">
                    @csrf
                    <label for="proposal-status-{{ $proposal->id }}">Actualizar estado</label>
                    <select id="proposal-status-{{ $proposal->id }}" name="status">
                        @foreach($proposalStatuses as $value => $label)
                            <option value="{{ $value }}" @selected($proposal->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit">Guardar</button>
                </form>
            </article>
        @empty
            <p>Aún no hay propuestas registradas.</p>
        @endforelse
    </section>

    <section class="panel-card">
        <h2>Historial de contacto y cambios</h2>
        @forelse($activities as $activity)
            <article class="panel-sales-entry">
                <p><strong>{{ $activityTypes[$activity->activity_type] ?? ucfirst($activity->activity_type) }}</strong> · {{ $activity->occurred_at }} · {{ $activity->operator_name ?: 'Operador anterior' }}</p>
                <p class="panel-preserve-lines">{{ $activity->description }}</p>
            </article>
        @empty
            <p>Aún no hay actividades registradas.</p>
        @endforelse
    </section>
</main>
</body>
</html>
