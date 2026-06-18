@extends('layouts.app')
@section('title', 'Actividades y Eventos')
@section('content')

@php
$tipoBadge = [
    'evento'       => ['blue',   'bi-calendar-event', 'Evento'],
    'curso'        => ['green',  'bi-book',            'Curso'],
    'taller'       => ['amber',  'bi-tools',           'Taller'],
    'conferencia'  => ['indigo', 'bi-mic',             'Conferencia'],
    'capacitacion' => ['teal',   'bi-mortarboard',     'Capacitación'],
];
$estadoBadge = [
    'borrador'   => ['#e2e8f0', '#475569', 'Borrador'],
    'activo'     => ['#dcfce7', '#166534', 'Activo'],
    'lleno'      => ['#fef3c7', '#92400e', 'Lleno'],
    'cancelado'  => ['#fee2e2', '#991b1b', 'Cancelado'],
    'finalizado' => ['#ede9fe', '#5b21b6', 'Finalizado'],
];
@endphp

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon blue"><i class="bi bi-person-badge"></i></div>
        <div>
            <h2>Actividades y Eventos</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Actividades</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('asistentes.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-people me-1"></i>Directorio
        </a>
        @if(auth()->user()->puede('act_asistentes','crear'))
        <a href="{{ route('actividades.create') }}" class="btn btn-navy btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nueva actividad
        </a>
        @endif
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-card-icon blue"><i class="bi bi-calendar3"></i></div><div><div class="stat-card-value">{{ $stats['total'] }}</div><div class="stat-card-label">Total actividades</div></div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-card-icon green"><i class="bi bi-check-circle"></i></div><div><div class="stat-card-value">{{ $stats['activas'] }}</div><div class="stat-card-label">Activas ahora</div></div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-card-icon amber"><i class="bi bi-calendar-month"></i></div><div><div class="stat-card-value">{{ $stats['mes'] }}</div><div class="stat-card-label">Este mes</div></div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-card-icon teal"><i class="bi bi-people"></i></div><div><div class="stat-card-value">{{ $stats['asistentes'] }}</div><div class="stat-card-label">Personas registradas</div></div></div>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" class="mb-3 d-flex gap-2 flex-wrap align-items-center">
    <div class="input-group" style="max-width:280px;">
        <span class="input-group-text" style="background:var(--bg-card);border-color:var(--border-color);"><i class="bi bi-search text-muted"></i></span>
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre…" value="{{ $q }}"
               style="background:var(--bg-card);border-color:var(--border-color);color:var(--text-main);">
    </div>
    <select name="tipo" class="form-select" style="width:auto;background:var(--bg-card);border-color:var(--border-color);color:var(--text-main);">
        <option value="">Todos los tipos</option>
        @foreach(App\Models\Actividad::tipos() as $t)
        <option value="{{ $t }}" @selected($tipo === $t)>{{ ucfirst($t) }}</option>
        @endforeach
    </select>
    <select name="estado" class="form-select" style="width:auto;background:var(--bg-card);border-color:var(--border-color);color:var(--text-main);">
        <option value="">Todos los estados</option>
        @foreach(App\Models\Actividad::estados() as $e)
        <option value="{{ $e }}" @selected($estado === $e)>{{ ucfirst($e) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-navy btn-sm"><i class="bi bi-funnel me-1"></i>Filtrar</button>
    @if($q || $tipo || $estado)
    <a href="{{ route('actividades.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x me-1"></i>Limpiar</a>
    @endif
</form>

{{-- Lista --}}
<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon blue"><i class="bi bi-list-ul"></i></div>
        Actividades
        <span class="badge ms-2" style="background:#e3f2fd;color:#1565c0;">{{ $actividades->total() }}</span>
        <span class="ms-auto small text-muted d-none d-md-inline" style="font-weight:400;font-size:.73rem;">
            <i class="bi bi-hand-index me-1"></i>Selecciona una fila para ver opciones
        </span>
    </div>
    <div class="table-responsive">
        <table class="table tabla-clickable">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Actividad</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Inscritos</th>
                    <th>Estado</th>
                    <th style="width:28px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($actividades as $act)
                @php
                    [$bgTipo, $iconTipo, $labelTipo] = $tipoBadge[$act->tipo] ?? ['secondary', 'bi-circle', $act->tipo];
                    [$bgEst, $colorEst, $labelEst]   = $estadoBadge[$act->estado] ?? ['#eee', '#333', $act->estado];
                    $cupoLabel = $act->cupo_maximo ? $act->inscritos_count . '/' . $act->cupo_maximo : $act->inscritos_count;
                    $pct = $act->cupo_maximo > 0 ? min(100, round(($act->inscritos_count / $act->cupo_maximo) * 100)) : 0;
                    $canEditAct   = auth()->user()->puede('act_asistentes','editar');
                    $canDeleteAct = auth()->user()->puede('act_asistentes','eliminar');
                    $rowData = [
                        'nombre'      => $act->nombre,
                        'codigo'      => $act->codigo,
                        'tipo'        => $labelTipo,
                        'icon_tipo'   => $iconTipo,
                        'estado'      => $labelEst,
                        'bg_est'      => $bgEst,
                        'color_est'   => $colorEst,
                        'fecha'       => $act->fecha_inicio->format('d/m/Y'),
                        'fecha_fin'   => ($act->fecha_fin && $act->fecha_fin != $act->fecha_inicio) ? $act->fecha_fin->format('d/m/Y') : null,
                        'hora_inicio' => $act->hora_inicio ? substr($act->hora_inicio, 0, 5) : null,
                        'hora_fin'    => $act->hora_fin ? substr($act->hora_fin, 0, 5) : null,
                        'instructor'  => $act->instructor,
                        'ubicacion'   => $act->ubicacion,
                        'inscritos'   => $act->inscritos_count,
                        'cupo'        => $act->cupo_maximo,
                        'cupo_label'  => $cupoLabel,
                        'pct'         => $pct,
                        'modalidad'   => $act->modalidad,
                        'show_url'    => route('actividades.show', $act),
                        'registro_url'=> route('registro.form', $act),
                        'edit_url'    => $canEditAct   ? route('actividades.edit', $act)    : null,
                        'destroy_url' => $canDeleteAct ? route('actividades.destroy', $act) : null,
                    ];
                @endphp
                <tr class="fila-clickable" data-json='@json($rowData)'>
                    <td><span class="small text-muted font-monospace">{{ $act->codigo }}</span></td>
                    <td>
                        <div class="fw-semibold" style="color:var(--text-main);">{{ $act->nombre }}</div>
                        @if($act->instructor)
                        <div class="small text-muted"><i class="bi bi-person me-1"></i>{{ $act->instructor }}</div>
                        @endif
                        @if($act->ubicacion)
                        <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $act->ubicacion }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge stat-card-icon {{ $bgTipo }}" style="font-size:.72rem;font-weight:600;padding:4px 10px;border-radius:20px;">
                            <i class="bi {{ $iconTipo }} me-1"></i>{{ $labelTipo }}
                        </span>
                    </td>
                    <td class="small">
                        <div>{{ $act->fecha_inicio->format('d/m/Y') }}</div>
                        @if($act->hora_inicio)
                        <div class="text-muted">{{ substr($act->hora_inicio, 0, 5) }}@if($act->hora_fin) – {{ substr($act->hora_fin, 0, 5) }}@endif</div>
                        @endif
                    </td>
                    <td>
                        <span class="fw-semibold">{{ $cupoLabel }}</span>
                        @if($act->cupo_maximo)
                        <div class="progress mt-1" style="height:4px;width:80px;background:#e0e0e0;">
                            <div class="progress-bar" style="width:{{ $pct }}%;background:var(--navy3);"></div>
                        </div>
                        @endif
                    </td>
                    <td>
                        <span style="background:{{ $bgEst }};color:{{ $colorEst }};border-radius:20px;padding:3px 10px;font-size:.75rem;font-weight:600;">
                            {{ $labelEst }}
                        </span>
                    </td>
                    <td><i class="bi bi-chevron-right text-muted" style="font-size:.75rem;"></i></td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-calendar-x"></i><p>No se encontraron actividades.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($actividades->hasPages())
    <div class="p-3 d-flex justify-content-center">
        {{ $actividades->links() }}
    </div>
    @endif
</div>

{{-- Form oculto para eliminar --}}
<form id="formEliminarAct" method="POST" class="d-none">
    @csrf @method('DELETE')
</form>

{{-- ══ PANEL LATERAL ═══════════════════════════════════════ --}}
<div class="offcanvas offcanvas-end d-flex flex-column" tabindex="-1" id="panelDetalle" style="width:380px;max-width:95vw;">
    <div class="offcanvas-header pb-3" style="background:linear-gradient(135deg,var(--navy),var(--navy3));">
        <div class="d-flex align-items-center gap-3 flex-grow-1 me-2" style="min-width:0;">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.15);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                <i class="bi bi-person-badge" id="panelIconAct"></i>
            </div>
            <div style="min-width:0;">
                <div class="text-white fw-bold lh-sm text-truncate" id="panelNombre" style="font-size:.95rem;"></div>
                <div id="panelCodigo" style="color:rgba(255,255,255,.65);font-size:.75rem;font-family:monospace;"></div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white flex-shrink-0" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body flex-grow-1 overflow-auto p-3" id="panelCuerpo"></div>
    <div class="p-3 border-top d-flex flex-column gap-2" id="panelFooter" style="background:var(--bg-card-alt);">
        <a id="panelBtnVer" href="#" class="btn btn-navy">
            <i class="bi bi-eye me-1"></i>Ver detalle completo
        </a>
        <div class="d-flex gap-2">
            <button id="panelBtnEnlace" class="btn btn-outline-success flex-fill" onclick="copiarEnlacePanel()">
                <i class="bi bi-link-45deg me-1"></i>Enlace público
            </button>
            <a id="panelBtnEditar" href="#" class="btn btn-outline-warning flex-fill d-none">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
        </div>
        <button id="panelBtnEliminar" class="btn btn-outline-danger d-none" onclick="eliminarPanel()">
            <i class="bi bi-trash me-1"></i>Eliminar actividad
        </button>
    </div>
</div>

@endsection

@section('scripts')
<style>
.fila-clickable { cursor: pointer; user-select: none; }
.fila-clickable:hover td { background: var(--bg-row-hover) !important; }
.fila-seleccionada td { background: #e8eaf620 !important; }
[data-theme="dark"] .fila-seleccionada td { background: #1a237e15 !important; }
.panel-campo { display:flex; gap:.65rem; align-items:flex-start; padding:.55rem 0; border-bottom:1px solid var(--border-color); }
.panel-campo:last-child { border-bottom: none; }
.panel-campo-icon { color:var(--text-muted); font-size:.85rem; width:16px; flex-shrink:0; margin-top:2px; }
.panel-campo-label { font-size:.68rem; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); }
.panel-campo-val { font-size:.875rem; color:var(--text-main); }
</style>
<script>
let _panelData = {};

function campo(icon, label, val) {
    if (!val && val !== 0) return '';
    return `<div class="panel-campo">
        <i class="bi ${icon} panel-campo-icon"></i>
        <div><div class="panel-campo-label">${label}</div><div class="panel-campo-val">${val}</div></div>
    </div>`;
}

document.querySelectorAll('.fila-clickable').forEach(function (fila) {
    fila.addEventListener('click', function () {
        const d = JSON.parse(this.dataset.json);
        _panelData = d;

        document.querySelectorAll('.fila-clickable').forEach(f => f.classList.remove('fila-seleccionada'));
        this.classList.add('fila-seleccionada');

        document.getElementById('panelNombre').textContent  = d.nombre;
        document.getElementById('panelCodigo').textContent  = d.codigo;
        document.getElementById('panelBtnVer').href         = d.show_url;

        const btnEd = document.getElementById('panelBtnEditar');
        const btnEl = document.getElementById('panelBtnEliminar');
        if (d.edit_url)    { btnEd.href = d.edit_url; btnEd.classList.remove('d-none'); } else btnEd.classList.add('d-none');
        if (d.destroy_url) { btnEl.classList.remove('d-none'); } else btnEl.classList.add('d-none');

        let horario = d.hora_inicio ? d.hora_inicio + (d.hora_fin ? ' – ' + d.hora_fin : '') : null;
        let fechaStr = d.fecha + (d.fecha_fin ? ' al ' + d.fecha_fin : '');

        let progreso = '';
        if (d.cupo) {
            progreso = `${d.cupo_label}
                <div class="progress mt-1" style="height:5px;background:#e0e0e0;border-radius:4px;">
                    <div class="progress-bar" style="width:${d.pct}%;background:var(--navy3);border-radius:4px;"></div>
                </div>`;
        } else {
            progreso = d.inscritos + ' inscritos (sin límite de cupo)';
        }

        let estadoHtml = `<span style="background:${d.bg_est};color:${d.color_est};border-radius:20px;padding:2px 10px;font-size:.78rem;font-weight:600;">${d.estado}</span>`;
        let tipoHtml   = `<span><i class="bi ${d.icon_tipo} me-1"></i>${d.tipo}</span>`;

        let html = '';
        html += campo('bi-tag',          'Tipo',       tipoHtml);
        html += campo('bi-circle-fill',  'Estado',     estadoHtml);
        html += campo('bi-calendar3',    'Fecha',      fechaStr);
        html += campo('bi-clock',        'Horario',    horario);
        html += campo('bi-person',       'Instructor', d.instructor);
        html += campo('bi-geo-alt',      'Ubicación',  d.ubicacion);
        html += campo('bi-wifi',         'Modalidad',  d.modalidad ? d.modalidad.charAt(0).toUpperCase() + d.modalidad.slice(1) : null);
        html += campo('bi-people',       'Inscritos',  progreso);

        document.getElementById('panelCuerpo').innerHTML = html;

        bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('panelDetalle')).show();
    });
});

function copiarEnlacePanel() {
    if (!_panelData.registro_url) return;
    navigator.clipboard.writeText(_panelData.registro_url).then(function() {
        const btn = document.getElementById('panelBtnEnlace');
        btn.innerHTML = '<i class="bi bi-check2 me-1"></i>¡Copiado!';
        setTimeout(() => btn.innerHTML = '<i class="bi bi-link-45deg me-1"></i>Enlace público', 2000);
    });
}

function eliminarPanel() {
    if (!_panelData.destroy_url) return;
    if (!confirm(`¿Eliminar la actividad «${_panelData.nombre}»?\nSe eliminarán también todas las inscripciones.`)) return;
    const form = document.getElementById('formEliminarAct');
    form.action = _panelData.destroy_url;
    form.submit();
}
</script>
@endsection
