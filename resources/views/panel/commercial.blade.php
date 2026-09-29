<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Gestión comercial | Panel de MINKA</title>@vite(['resources/css/app.css'])
@include('partials.favicon')
</head>
<body class="panel-body">
<header class="panel-header"><strong>ORBYNIA · Panel de MINKA</strong><nav class="panel-header-actions"><a href="{{ route('minka.dashboard') }}">Inicio del panel</a><form method="post" action="{{ route('minka.logout') }}">@csrf<button type="submit">Salir</button></form></nav></header>
<main class="panel-main">
    <p class="panel-eyebrow">SECCIÓN 01</p><h1>Gestión comercial</h1>
    <p>Contactos, demostraciones y evaluaciones. Los seguimientos aparecen junto a cada empresa.</p>
    <p class="panel-summary-line"><strong>{{ $dueFollowUpsCount }}</strong> seguimientos próximos o vencidos</p>
    @if(session('panel_success'))<div class="form-success" role="status">{{ session('panel_success') }}</div>@endif
    @if(session('panel_warning'))<div class="form-alert" role="status">{{ session('panel_warning') }}</div>@endif
    <div class="panel-table-wrap"><table>
        <thead><tr><th>Empresa y contacto</th><th>Origen</th><th>Etapa</th><th>Responsable</th><th>Próximo seguimiento</th><th></th></tr></thead>
        <tbody>
        @forelse($records as $item)
            @php $isApplication = $item->record_type === 'solicitud'; $nextFollowUp = $item->next_follow_up_at ? \Illuminate\Support\Carbon::parse($item->next_follow_up_at) : null; @endphp
            <tr>
                <td><strong>{{ $item->company_name }}</strong><br>{{ $item->contact_name }}<br><small>{{ $item->email }} · {{ $item->phone }}</small></td>
                <td>{{ $isApplication ? 'Evaluación · '.ucfirst($item->detail) : 'Contacto · '.\Illuminate\Support\Str::headline($item->detail) }}</td>
                <td>{{ $salesStages[$item->stage] ?? $item->stage }}</td><td>{{ $item->sales_owner_name ?: 'Sin asignar' }}</td>
                <td>@if($nextFollowUp)<span @class(['panel-overdue' => $nextFollowUp->isPast()])>{{ $nextFollowUp->format('d/m/Y H:i') }}</span>@else<span>Sin fecha</span>@endif</td>
                <td class="panel-row-actions">@if($isApplication)<a href="{{ route('minka.application', $item->id) }}">Abrir solicitud</a>@endif<a href="{{ route('minka.sales.show', [$item->record_type, $item->id]) }}">Gestionar seguimiento</a></td>
            </tr>
        @empty<tr><td colspan="6">Todavía no hay contactos ni evaluaciones.</td></tr>@endforelse
        </tbody>
    </table></div>
    {{ $records->links() }}
</main></body></html>