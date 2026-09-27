<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Solicitudes | Panel de MINKA</title>
    @vite(['resources/css/app.css'])
</head>
<body class="panel-body">
<header class="panel-header">
    <strong>ORBYNIA · Panel de MINKA</strong>
    <form method="post" action="{{ route('minka.logout') }}">@csrf<button type="submit">Salir</button></form>
</header>
<main class="panel-main">
    <h1>Gestión de ORBYNIA</h1>
    <p>Revisa solicitudes comerciales, reclamaciones y solicitudes de eliminación. La solicitud central no crea un usuario en el ERP.</p>
    @if(session('panel_success'))<div class="form-success" role="status">{{ session('panel_success') }}</div>@endif
    @if(session('panel_warning'))<div class="form-alert" role="status">{{ session('panel_warning') }}</div>@endif

    <section id="seguimientos">
        <h2>Seguimientos próximos o vencidos</h2>
        <p>Se muestran las acciones con fecha hasta los próximos siete días. Abre cada ficha para registrar lo ocurrido o cambiar la fecha.</p>
        <div class="panel-table-wrap"><table>
            <thead><tr><th>Empresa</th><th>Origen</th><th>Próxima acción</th><th></th></tr></thead>
            <tbody>
            @forelse($dueFollowUps as $item)
                <tr>
                    <td>{{ $item->company_name }}</td>
                    <td>{{ $item->type === 'contacto' ? 'Contacto' : 'Evaluación' }}</td>
                    <td><span @class(['panel-overdue' => \Illuminate\Support\Carbon::parse($item->next_follow_up_at)->isPast()])>{{ \Illuminate\Support\Carbon::parse($item->next_follow_up_at)->format('d/m/Y H:i') }}</span></td>
                    <td><a href="{{ route('minka.sales.show', [$item->type, $item->id]) }}">Gestionar</a></td>
                </tr>
            @empty
                <tr><td colspan="4">No hay seguimientos próximos registrados.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </section>

    <section id="reclamaciones">
        <h2>Reclamaciones</h2>
        <div class="panel-table-wrap"><table>
            <thead><tr><th>Referencia</th><th>Consumidor</th><th>Asunto</th><th>Estado</th><th>Responsable</th><th>Fecha</th><th></th></tr></thead>
            <tbody>
            @forelse($complaints as $item)
                <tr>
                    <td>{{ $item->reference }}</td>
                    <td>{{ $item->first_name }} {{ $item->last_name }}<br><small>{{ $item->email }}</small></td>
                    <td>{{ ucfirst($item->complaint_type) }}<br><small>{{ \Illuminate\Support\Str::limit($item->item_description, 70) }}</small></td>
                    <td>{{ $caseStatuses['complaint'][$item->status] ?? $item->status }}</td>
                    <td>{{ $item->responsible_name ?: 'Sin asignar' }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td><a href="{{ route('minka.complaint', $item->id) }}">Gestionar</a></td>
                </tr>
            @empty
                <tr><td colspan="7">Todavía no hay reclamaciones.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        {{ $complaints->links() }}
    </section>

    <section id="eliminaciones">
        <h2>Solicitudes de eliminación</h2>
        <div class="panel-table-wrap"><table>
            <thead><tr><th>Referencia</th><th>Solicitante</th><th>Empresa</th><th>Estado</th><th>Responsable</th><th>Fecha</th><th></th></tr></thead>
            <tbody>
            @forelse($deletionRequests as $item)
                <tr>
                    <td>{{ $item->reference }}</td>
                    <td>{{ $item->full_name }}<br><small>{{ $item->email }}</small></td>
                    <td>{{ $item->company }}</td>
                    <td>{{ $caseStatuses['deletion'][$item->status] ?? $item->status }}</td>
                    <td>{{ $item->responsible_name ?: 'Sin asignar' }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td><a href="{{ route('minka.deletion', $item->id) }}">Gestionar</a></td>
                </tr>
            @empty
                <tr><td colspan="7">Todavía no hay solicitudes de eliminación.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        {{ $deletionRequests->links() }}
    </section>

    <section>
        <h2>Evaluaciones</h2>
        <div class="panel-table-wrap"><table>
            <thead><tr><th>Empresa</th><th>Contacto</th><th>Plan</th><th>Alta</th><th>Etapa comercial</th><th>Resp. MINKA</th><th>Próxima acción</th><th></th></tr></thead>
            <tbody>
            @forelse($applications as $item)
                <tr><td>{{ $item->company_name }}</td><td>{{ $item->first_name }} {{ $item->last_name }}<br><small>{{ $item->email }}</small></td><td>{{ ucfirst($item->plan_interest) }}</td><td>{{ $item->status }}<br><small>Correo: {{ ['pending' => 'Pendiente', 'sending' => 'En proceso', 'sent' => 'Enviado', 'logged' => 'Solo local', 'failed' => 'Falló'][$item->verification_mail_status] ?? $item->verification_mail_status }}</small></td><td>{{ $salesStages[$item->sales_stage] ?? $item->sales_stage }}</td><td>{{ $item->sales_owner_name ?: 'Sin asignar' }}</td><td>{{ $item->next_follow_up_at ?: 'Sin fecha' }}</td><td><a href="{{ route('minka.application', $item->id) }}">Alta</a><br><a href="{{ route('minka.sales.show', ['solicitud', $item->id]) }}">Seguimiento</a></td></tr>
            @empty
                <tr><td colspan="8">Todavía no hay solicitudes.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        {{ $applications->links() }}
    </section>

    <section id="contactos">
        <h2>Contactos y demostraciones</h2>
        <div class="panel-table-wrap"><table>
            <thead><tr><th>Empresa</th><th>Persona</th><th>Motivo</th><th>Etapa</th><th>Resp. MINKA</th><th>Próxima acción</th><th></th></tr></thead>
            <tbody>
            @forelse($leads as $lead)
                <tr><td>{{ $lead->company_name }}</td><td>{{ $lead->contact_name }}<br><small>{{ $lead->email }} · {{ $lead->phone }}</small></td><td>{{ $lead->source }}</td><td>{{ $salesStages[$lead->status] ?? $lead->status }}</td><td>{{ $lead->sales_owner_name ?: 'Sin asignar' }}</td><td>{{ $lead->next_follow_up_at ?: 'Sin fecha' }}</td><td><a href="{{ route('minka.sales.show', ['contacto', $lead->id]) }}">Gestionar</a></td></tr>
            @empty
                <tr><td colspan="7">Todavía no hay contactos.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        {{ $leads->links() }}
    </section>
</main>
</body>
</html>
