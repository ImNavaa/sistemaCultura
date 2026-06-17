@extends('layouts.app')
@section('title', $actividad->nombre)
@section('content')

@php
$estadoBadge = [
    'borrador'   => ['#e2e8f0', '#475569'],
    'activo'     => ['#dcfce7', '#166534'],
    'lleno'      => ['#fef3c7', '#92400e'],
    'cancelado'  => ['#fee2e2', '#991b1b'],
    'finalizado' => ['#ede9fe', '#5b21b6'],
];
[$bgEst, $colorEst] = $estadoBadge[$actividad->estado] ?? ['#eee', '#333'];
@endphp

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon blue"><i class="bi bi-person-badge"></i></div>
        <div>
            <h2>{{ $actividad->nombre }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('actividades.index') }}">Actividades</a></li>
                    <li class="breadcrumb-item active">{{ $actividad->codigo }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-sm btn-outline-success" title="Copiar enlace de registro público"
                onclick="copiarEnlaceRegistro(this)">
            <i class="bi bi-link-45deg me-1"></i>Enlace público
        </button>
        <a href="{{ route('actividades.export-csv', $actividad) }}" class="btn btn-sm btn-outline-secondary" title="Exportar CSV">
            <i class="bi bi-filetype-csv me-1"></i>CSV
        </a>
        <a href="{{ route('actividades.export-pdf', $actividad) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Exportar PDF">
            <i class="bi bi-filetype-pdf me-1"></i>PDF
        </a>
        @if(auth()->user()->puede('act_asistentes','editar'))
        <a href="{{ route('actividades.edit', $actividad) }}" class="btn btn-sm btn-outline-warning">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        @endif
        @if(auth()->user()->puede('act_asistentes','crear'))
        <button class="btn btn-navy btn-sm" data-bs-toggle="modal" data-bs-target="#modalInscribir">
            <i class="bi bi-person-plus me-1"></i>Inscribir persona
        </button>
        @endif
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    @foreach($errors->all() as $e)<div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach
</div>
@endif

<div class="row g-3 mb-4">
    {{-- Info actividad --}}
    <div class="col-md-4">
        <div class="data-card h-100">
            <div class="data-card-header">
                <div class="header-icon blue"><i class="bi bi-info-circle"></i></div>
                Información
            </div>
            <div class="p-3">
                <ul class="info-list">
                    <li><span class="info-key">Código</span><span class="info-val font-monospace small">{{ $actividad->codigo }}</span></li>
                    <li><span class="info-key">Tipo</span><span class="info-val">{{ ucfirst($actividad->tipo) }}</span></li>
                    <li><span class="info-key">Estado</span>
                        <span style="background:{{ $bgEst }};color:{{ $colorEst }};border-radius:20px;padding:2px 10px;font-size:.75rem;font-weight:600;">
                            {{ ucfirst($actividad->estado) }}
                        </span>
                    </li>
                    <li><span class="info-key">Fecha</span>
                        <span class="info-val">{{ $actividad->fecha_inicio->format('d/m/Y') }}
                        @if($actividad->fecha_fin && $actividad->fecha_fin != $actividad->fecha_inicio)
                         – {{ $actividad->fecha_fin->format('d/m/Y') }}
                        @endif</span>
                    </li>
                    @if($actividad->hora_inicio)
                    <li><span class="info-key">Horario</span>
                        <span class="info-val">{{ substr($actividad->hora_inicio,0,5) }}
                        @if($actividad->hora_fin) – {{ substr($actividad->hora_fin,0,5) }}@endif</span>
                    </li>
                    @endif
                    @if($actividad->ubicacion)
                    <li><span class="info-key">Ubicación</span><span class="info-val">{{ $actividad->ubicacion }}</span></li>
                    @endif
                    <li><span class="info-key">Modalidad</span><span class="info-val">{{ ucfirst($actividad->modalidad) }}</span></li>
                    @if($actividad->instructor)
                    <li><span class="info-key">Instructor</span><span class="info-val">{{ $actividad->instructor }}</span></li>
                    @endif
                    <li><span class="info-key">Cupo</span>
                        <span class="info-val">
                            @if($actividad->cupo_maximo)
                                {{ $totalInscritos }} / {{ $actividad->cupo_maximo }}
                            @else
                                {{ $totalInscritos }} (sin límite)
                            @endif
                        </span>
                    </li>
                </ul>
                @if($actividad->descripcion)
                <div class="small text-muted mt-3">{{ $actividad->descripcion }}</div>
                @endif
                @if($actividad->requisitos)
                <div class="mt-3 p-2 rounded" style="background:#fffbeb;border:1px solid #fde68a;">
                    <div class="small fw-semibold mb-1" style="color:#92400e;">
                        <i class="bi bi-clipboard-check me-1"></i>Requisitos
                    </div>
                    <div class="small" style="color:#78350f;white-space:pre-line;">{{ $actividad->requisitos }}</div>
                </div>
                @endif
                @if($actividad->documento_pdf)
                <div class="mt-2 p-2 rounded d-flex align-items-center gap-2"
                     style="background:#fff5f5;border:1px solid #fecaca;">
                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                    <span class="small fw-semibold" style="color:#991b1b;">PDF adjunto al correo</span>
                    <span class="text-muted small ms-auto">se envía automáticamente al registrarse</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="col-md-8">
        <div class="row g-3 mb-3">
            <div class="col-4">
                <div class="stat-card">
                    <div class="stat-card-icon blue"><i class="bi bi-person-check"></i></div>
                    <div><div class="stat-card-value">{{ $totalInscritos }}</div><div class="stat-card-label">Inscritos</div></div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card">
                    <div class="stat-card-icon green"><i class="bi bi-check2-circle"></i></div>
                    <div><div class="stat-card-value">{{ $totalAsistieron }}</div><div class="stat-card-label">Asistieron</div></div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card">
                    <div class="stat-card-icon {{ $porcentaje >= 75 ? 'green' : ($porcentaje >= 40 ? 'amber' : 'red') }}">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div><div class="stat-card-value">{{ $porcentaje }}%</div><div class="stat-card-label">Asistencia</div></div>
                </div>
            </div>
        </div>

        {{-- Barra progreso asistencia --}}
        @if($totalInscritos > 0)
        <div class="data-card p-3">
            <div class="d-flex justify-content-between small text-muted mb-2">
                <span>Asistencia al evento</span>
                <span>{{ $totalAsistieron }} de {{ $totalInscritos }}</span>
            </div>
            <div class="progress" style="height:10px;background:#e0e0e0;border-radius:10px;">
                <div class="progress-bar" role="progressbar"
                     style="width:{{ $porcentaje }}%;background:{{ $porcentaje >= 75 ? '#2e7d32' : ($porcentaje >= 40 ? '#e65100' : '#c62828') }};border-radius:10px;transition:width .4s;">
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Tabla de inscritos --}}
<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon blue"><i class="bi bi-list-check"></i></div>
        Lista de inscritos
        <span class="badge ms-2" style="background:#e3f2fd;color:#1565c0;">{{ $totalInscritos }}</span>
        <span class="ms-auto small text-muted d-none d-md-inline" style="font-weight:400;font-size:.73rem;">
            <i class="bi bi-hand-index me-1"></i>Selecciona una fila para ver opciones
        </span>
    </div>

    {{-- Buscador rápido --}}
    <div class="p-3 pb-0">
        <input type="search" id="buscarInscritos" class="form-control form-control-sm" placeholder="Filtrar por nombre, email, institución…"
               style="max-width:360px;background:var(--bg-card);border-color:var(--border-color);color:var(--text-main);">
    </div>

    <div class="table-responsive">
        <table class="table tabla-clickable" id="tablaInscritos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Folio</th>
                    <th>Persona</th>
                    <th>Institución</th>
                    <th>Contacto</th>
                    <th class="text-center">Asistió</th>
                    <th style="width:28px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($inscripciones as $i => $insc)
                @if($insc->estado === 'inscrito')
                @php
                    $checkinHora = $insc->checkin ? $insc->checkin->hora_checkin->format('H:i') : null;
                @endphp
                <tr class="insc-row fila-clickable"
                    data-buscar="{{ strtolower($insc->asistente->nombre . ' ' . $insc->asistente->apellidos . ' ' . $insc->asistente->email . ' ' . $insc->asistente->institucion) }}"
                    data-json='@json([
                        "insc_id"      => $insc->id,
                        "folio"        => $insc->folio,
                        "nombre"       => $insc->asistente->nombreCompleto(),
                        "iniciales"    => $insc->asistente->iniciales(),
                        "email"        => $insc->asistente->email,
                        "telefono"     => $insc->asistente->telefono,
                        "institucion"  => $insc->asistente->institucion,
                        "ciudad"       => $insc->asistente->ciudad,
                        "ocupacion"    => $insc->asistente->ocupacion,
                        "checkin"      => (bool)$insc->checkin,
                        "checkin_hora" => $checkinHora,
                        "notas"        => $insc->notas ?? null,
                        "show_ast_url" => route("asistentes.show", $insc->asistente),
                        "checkin_url"  => auth()->user()->puede("act_asistentes","editar") && !$insc->checkin
                                          ? route("inscripciones.checkin", $insc) : null,
                        "destroy_url"  => auth()->user()->puede("act_asistentes","eliminar")
                                          ? route("inscripciones.destroy", $insc) : null,
                    ])'>
                    <td class="text-muted small">{{ $i + 1 }}</td>
                    <td><span class="font-monospace small">{{ $insc->folio }}</span></td>
                    <td>
                        <div class="fw-semibold" style="color:var(--text-main);">{{ $insc->asistente->nombreCompleto() }}</div>
                        @if($insc->asistente->email)
                        <div class="small text-muted">{{ $insc->asistente->email }}</div>
                        @endif
                    </td>
                    <td class="small text-muted">
                        {{ $insc->asistente->institucion ?? '—' }}
                        @if($insc->asistente->ciudad)<div>{{ $insc->asistente->ciudad }}</div>@endif
                    </td>
                    <td class="small">{{ $insc->asistente->telefono ?? '—' }}</td>
                    <td class="text-center">
                        @if($insc->checkin)
                            <span class="badge" style="background:#dcfce7;color:#166534;font-size:.72rem;">
                                <i class="bi bi-check2"></i> {{ $insc->checkin->hora_checkin->format('H:i') }}
                            </span>
                        @else
                            <span class="badge" style="background:#f1f5f9;color:#64748b;font-size:.72rem;">Pendiente</span>
                        @endif
                    </td>
                    <td><i class="bi bi-chevron-right text-muted" style="font-size:.75rem;"></i></td>
                </tr>
                @endif
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-person-plus"></i><p>Sin inscritos aún. Usa el botón "Inscribir persona".</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Forms ocultos para acciones desde el panel --}}
<form id="formCheckin" method="POST" class="d-none">@csrf @method('PATCH')</form>
<form id="formEliminarInsc" method="POST" class="d-none">@csrf @method('DELETE')</form>

{{-- ══ PANEL LATERAL INSCRIPCIONES ════════════════════════ --}}
<div class="offcanvas offcanvas-end d-flex flex-column" tabindex="-1" id="panelInscripcion" style="width:370px;max-width:95vw;">
    <div class="offcanvas-header pb-3" style="background:linear-gradient(135deg,var(--navy),var(--navy3));">
        <div class="d-flex align-items-center gap-3">
            <span id="insc-avatar" style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.18);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0;"></span>
            <div>
                <div class="text-white fw-bold lh-sm" id="insc-nombre" style="font-size:1rem;"></div>
                <div id="insc-folio" style="color:rgba(255,255,255,.65);font-size:.75rem;font-family:monospace;"></div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body flex-grow-1 overflow-auto p-3" id="insc-cuerpo"></div>
    <div class="p-3 border-top d-flex flex-column gap-2" style="background:var(--bg-card-alt);">
        <a id="insc-btn-ver" href="#" class="btn btn-outline-primary">
            <i class="bi bi-person-lines-fill me-1"></i>Ver perfil del asistente
        </a>
        <button id="insc-btn-checkin" class="btn btn-success d-none" onclick="hacerCheckin()">
            <i class="bi bi-check2-square me-1"></i>Registrar asistencia (Check-in)
        </button>
        <button id="insc-btn-eliminar" class="btn btn-outline-danger d-none" onclick="eliminarInscripcion()">
            <i class="bi bi-x-circle me-1"></i>Cancelar inscripción
        </button>
    </div>
</div>

{{-- Modal inscribir --}}
@if(auth()->user()->puede('act_asistentes','crear'))
<div class="modal fade" id="modalInscribir" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Inscribir persona</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Tabs: buscar existente / nueva persona --}}
                <ul class="nav nav-pills mb-3" id="inscribTabs">
                    <li class="nav-item"><button class="nav-link active" data-tab="buscar">Buscar persona existente</button></li>
                    <li class="nav-item"><button class="nav-link ms-1" data-tab="nueva">Nueva persona</button></li>
                </ul>

                {{-- Panel: buscar --}}
                <div id="panelBuscar">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchAsistente" class="form-control" placeholder="Nombre, apellidos o email…">
                    </div>
                    <div id="resultadosBusqueda" class="mb-3" style="max-height:280px;overflow-y:auto;"></div>
                    <div id="seleccionadoInfo" class="d-none alert" style="background:#e3f2fd;color:#1565c0;border-radius:10px;border:none;">
                        <i class="bi bi-person-check-fill me-2"></i><span id="seleccionadoNombre"></span>
                        <button type="button" class="btn btn-sm ms-2" style="padding:1px 8px;font-size:.75rem;" id="btnCambiarPerson">Cambiar</button>
                    </div>
                    <form action="{{ route('inscripciones.store') }}" method="POST" id="formBuscar">
                        @csrf
                        <input type="hidden" name="actividad_id" value="{{ $actividad->id }}">
                        <input type="hidden" name="asistente_id" id="hiddenAsistenteId">
                        <div class="mb-3">
                            <label class="form-label">Notas (opcional)</label>
                            <input type="text" name="notas" class="form-control" placeholder="Observaciones de esta inscripción">
                        </div>
                        <button type="submit" class="btn btn-navy" id="btnInscribirExistente" disabled>
                            <i class="bi bi-check-lg me-1"></i>Inscribir
                        </button>
                    </form>
                </div>

                {{-- Panel: nueva persona --}}
                <div id="panelNueva" class="d-none">
                    <form action="{{ route('inscripciones.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="actividad_id" value="{{ $actividad->id }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="apellidos" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Institución / Organización</label>
                                <input type="text" name="institucion" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <input type="text" name="notas" class="form-control" placeholder="Observaciones de esta inscripción">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-navy">
                                    <i class="bi bi-person-plus me-1"></i>Registrar e inscribir
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
// Filtro rápido en la tabla
document.getElementById('buscarInscritos').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaInscritos .insc-row').forEach(row => {
        row.style.display = !q || row.dataset.buscar.includes(q) ? '' : 'none';
    });
});

// Tabs del modal
document.querySelectorAll('#inscribTabs .nav-link').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('#inscribTabs .nav-link').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const tab = this.dataset.tab;
        document.getElementById('panelBuscar').classList.toggle('d-none', tab !== 'buscar');
        document.getElementById('panelNueva').classList.toggle('d-none', tab !== 'nueva');
    });
});

// Búsqueda AJAX de asistentes
let debounceTimer;
document.getElementById('searchAsistente')?.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    const q = this.value.trim();
    if (q.length < 2) { document.getElementById('resultadosBusqueda').innerHTML = ''; return; }
    debounceTimer = setTimeout(() => {
        fetch(`{{ route('asistentes.buscar') }}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                const box = document.getElementById('resultadosBusqueda');
                if (!data.length) {
                    box.innerHTML = '<div class="text-muted small p-2">Sin resultados. Usa la pestaña "Nueva persona".</div>';
                    return;
                }
                box.innerHTML = data.map(a => `
                    <div class="d-flex align-items-center gap-3 p-2 border-bottom resultado-item" style="cursor:pointer;border-radius:8px;" data-id="${a.id}" data-nombre="${a.nombre} ${a.apellidos}" data-email="${a.email || ''}">
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;">
                            ${(a.nombre[0] + a.apellidos[0]).toUpperCase()}
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:.9rem;">${a.nombre} ${a.apellidos}</div>
                            <div class="small text-muted">${a.email || ''} ${a.institucion ? '· ' + a.institucion : ''}</div>
                        </div>
                    </div>
                `).join('');

                box.querySelectorAll('.resultado-item').forEach(el => {
                    el.addEventListener('mouseenter', function () { this.style.background = 'var(--bg-card-alt)'; });
                    el.addEventListener('mouseleave', function () { this.style.background = ''; });
                    el.addEventListener('click', function () {
                        const id     = this.dataset.id;
                        const nombre = this.dataset.nombre;
                        document.getElementById('hiddenAsistenteId').value = id;
                        document.getElementById('seleccionadoNombre').textContent = nombre;
                        document.getElementById('seleccionadoInfo').classList.remove('d-none');
                        document.getElementById('btnInscribirExistente').disabled = false;
                        document.getElementById('resultadosBusqueda').innerHTML = '';
                        document.getElementById('searchAsistente').value = nombre;
                    });
                });
            });
    }, 300);
});

function copiarEnlaceRegistro(btn) {
    const url = '{{ route('registro.form', $actividad) }}';
    navigator.clipboard.writeText(url).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check me-1"></i>¡Copiado!';
        btn.classList.replace('btn-outline-success', 'btn-success');
        setTimeout(() => {
            btn.innerHTML = orig;
            btn.classList.replace('btn-success', 'btn-outline-success');
        }, 2000);
    });
}

document.getElementById('btnCambiarPerson')?.addEventListener('click', function () {
    document.getElementById('hiddenAsistenteId').value = '';
    document.getElementById('seleccionadoInfo').classList.add('d-none');
    document.getElementById('btnInscribirExistente').disabled = true;
    document.getElementById('searchAsistente').value = '';
    document.getElementById('searchAsistente').focus();
});

// ── Panel lateral de inscripciones ────────────────────────
let _inscData = {};

function campoPan(icon, label, val) {
    if (!val && val !== 0) return '';
    return `<div style="display:flex;gap:.65rem;align-items:flex-start;padding:.55rem 0;border-bottom:1px solid var(--border-color);">
        <i class="bi ${icon}" style="color:var(--text-muted);font-size:.85rem;width:16px;flex-shrink:0;margin-top:2px;"></i>
        <div>
            <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">${label}</div>
            <div style="font-size:.875rem;color:var(--text-main);">${val}</div>
        </div>
    </div>`;
}

document.querySelectorAll('.fila-clickable').forEach(function (fila) {
    fila.addEventListener('click', function () {
        const d = JSON.parse(this.dataset.json);
        _inscData = d;

        document.querySelectorAll('.fila-clickable').forEach(f => f.classList.remove('fila-seleccionada'));
        this.classList.add('fila-seleccionada');

        document.getElementById('insc-avatar').textContent = d.iniciales || d.nombre.charAt(0).toUpperCase();
        document.getElementById('insc-nombre').textContent = d.nombre;
        document.getElementById('insc-folio').textContent  = 'Folio: ' + d.folio;
        document.getElementById('insc-btn-ver').href       = d.show_ast_url;

        const btnCi = document.getElementById('insc-btn-checkin');
        const btnEl = document.getElementById('insc-btn-eliminar');

        d.checkin_url ? btnCi.classList.remove('d-none') : btnCi.classList.add('d-none');
        d.destroy_url ? btnEl.classList.remove('d-none') : btnEl.classList.add('d-none');

        let checkinHtml = d.checkin
            ? `<span class="badge" style="background:#dcfce7;color:#166534;"><i class="bi bi-check2 me-1"></i>Asistió a las ${d.checkin_hora}</span>`
            : `<span class="badge" style="background:#f1f5f9;color:#64748b;">Pendiente</span>`;

        let html = '';
        html += campoPan('bi-envelope',    'Email',       d.email);
        html += campoPan('bi-telephone',   'Teléfono',    d.telefono);
        html += campoPan('bi-building',    'Institución', d.institucion);
        html += campoPan('bi-briefcase',   'Ocupación',   d.ocupacion);
        html += campoPan('bi-geo-alt',     'Ciudad',      d.ciudad);
        html += campoPan('bi-check-circle','Asistencia',  checkinHtml);
        if (d.notas) html += campoPan('bi-chat-text', 'Notas', d.notas);

        document.getElementById('insc-cuerpo').innerHTML = html || '<p class="text-muted small mt-2">Sin datos adicionales.</p>';

        bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('panelInscripcion')).show();
    });
});

function hacerCheckin() {
    if (!_inscData.checkin_url) return;
    if (!confirm(`¿Registrar asistencia de ${_inscData.nombre}?`)) return;
    const form = document.getElementById('formCheckin');
    form.action = _inscData.checkin_url;
    form.submit();
}

function eliminarInscripcion() {
    if (!_inscData.destroy_url) return;
    if (!confirm(`¿Cancelar la inscripción de ${_inscData.nombre}?`)) return;
    const form = document.getElementById('formEliminarInsc');
    form.action = _inscData.destroy_url;
    form.submit();
}
</script>
<style>
.fila-clickable { cursor: pointer; user-select: none; }
.fila-clickable:hover td { background: var(--bg-row-hover) !important; }
.fila-seleccionada td { background: #e8eaf620 !important; }
[data-theme="dark"] .fila-seleccionada td { background: #1a237e15 !important; }
</style>
@endsection
