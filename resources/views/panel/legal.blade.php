<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Atención legal | Panel de MINKA</title>@vite(['resources/css/app.css'])
@include('partials.favicon')
</head>
<body class="panel-body">
<header class="panel-header"><strong>ORBYNIA · Panel de MINKA</strong><nav class="panel-header-actions"><a href="{{ route('minka.dashboard') }}">Inicio del panel</a><form method="post" action="{{ route('minka.logout') }}">@csrf<button type="submit">Salir</button></form></nav></header>
<main class="panel-main">
    <p class="panel-eyebrow">SECCIÓN 02</p><h1>Atención legal</h1><p>Reclamaciones y solicitudes de eliminación de cuenta.</p>
    @if(session('panel_success'))<div class="form-success" role="status">{{ session('panel_success') }}</div>@endif
    @if(session('panel_warning'))<div class="form-alert" role="status">{{ session('panel_warning') }}</div>@endif
    <div class="panel-table-wrap"><table>
        <thead><tr><th>Referencia</th><th>Tipo</th><th>Solicitante</th><th>Asunto</th><th>Estado</th><th>Responsable</th><th>Fecha</th><th></th></tr></thead>
        <tbody>
        @forelse($cases as $item)
            @php $isComplaint = $item->request_type === 'complaint'; @endphp
            <tr><td>{{ $item->reference }}</td><td>{{ $isComplaint ? 'Reclamación' : 'Eliminación de cuenta' }}</td>
                <td>{{ $item->requester_name }}<br><small>{{ $item->email }}</small></td><td>{{ $item->subject }}</td>
                <td>{{ $caseStatuses[$item->request_type][$item->status] ?? $item->status }}</td><td>{{ $item->responsible_name ?: 'Sin asignar' }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                <td><a href="{{ $isComplaint ? route('minka.complaint', $item->id) : route('minka.deletion', $item->id) }}">Gestionar</a></td></tr>
        @empty<tr><td colspan="8">Todavía no hay casos recibidos.</td></tr>@endforelse
        </tbody>
    </table></div>
    {{ $cases->links() }}
</main></body></html>