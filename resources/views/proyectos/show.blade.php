@extends('layouts.app')

@section('title', $proyecto->titulo)

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Encabezado del proyecto --}}
    <div class="card border-0 shadow-sm mb-4" style="border-left: 5px solid {{ $proyecto->color ?? '#3a7bd5' }} !important;">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <a href="{{ route('proyectos.index') }}" class="text-muted small">
                            <i class="bi bi-kanban me-1"></i>Proyectos
                        </a>
                        <i class="bi bi-chevron-right small text-muted"></i>
                        <span class="small fw-semibold">{{ $proyecto->titulo }}</span>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $proyecto->titulo }}</h4>
                    @if($proyecto->descripcion)
                    <p class="text-muted mb-2 small">{{ $proyecto->descripcion }}</p>
                    @endif
                    <div class="d-flex align-items-center gap-3 flex-wrap small">
                        @php
                            $estadoBadge = [
                                'activo'     => 'success',
                                'pausado'    => 'warning text-dark',
                                'completado' => 'primary',
                                'cancelado'  => 'secondary',
                            ][$proyecto->estado] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $estadoBadge }}">{{ ucfirst($proyecto->estado) }}</span>
                        @if($proyecto->fecha_limite)
                        <span class="text-muted">
                            <i class="bi bi-calendar-event me-1"></i>{{ $proyecto->fecha_limite->format('d/m/Y') }}
                            @if($proyecto->estado === 'activo' && $proyecto->fecha_limite->isPast())
                            <span class="badge bg-danger ms-1">Vencido</span>
                            @endif
                        </span>
                        @endif
                        <span class="text-muted">
                            <i class="bi bi-person me-1"></i>{{ $proyecto->creador->nombre ?? $proyecto->creador->name }}
                        </span>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-end gap-2">
                    {{-- Progreso --}}
                    @php
                        $total      = $proyecto->tareas->count();
                        $completadas= $proyecto->tareas->where('estado','completada')->count();
                        $progreso   = $total > 0 ? intval(round($completadas / $total * 100)) : 0;
                    @endphp
                    <div class="text-end small mb-1">
                        {{ $completadas }}/{{ $total }} tareas &mdash; <strong>{{ $progreso }}%</strong>
                    </div>
                    <div class="progress" style="height:8px;width:180px;">
                        <div class="progress-bar" role="progressbar"
                             style="width:{{ $progreso }}%; background:{{ $proyecto->color ?? '#3a7bd5' }}"
                             aria-valuenow="{{ $progreso }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex gap-2 mt-1">
                        @if(auth()->user()->puede('proyectos','editar'))
                        <a href="{{ route('proyectos.edit', $proyecto) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>
                        @endif
                        @if(auth()->user()->puede('proyectos','eliminar'))
                        <form action="{{ route('proyectos.destroy', $proyecto) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar este proyecto y todas sus tareas?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3" id="proyectoTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista"
                    type="button" role="tab">
                <i class="bi bi-list-task me-1"></i>Lista
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="kanban-tab" data-bs-toggle="tab" data-bs-target="#kanban"
                    type="button" role="tab">
                <i class="bi bi-kanban me-1"></i>Tablero Kanban
            </button>
        </li>
    </ul>

    <div class="tab-content" id="proyectoTabsContent">

        {{-- ===== TAB LISTA ===== --}}
        <div class="tab-pane fade show active" id="lista" role="tabpanel">

            {{-- Filtros + botón nueva tarea --}}
            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <select id="filtroEstado" class="form-select form-select-sm" style="width:auto">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_progreso">En Progreso</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                    <select id="filtroPrioridad" class="form-select form-select-sm" style="width:auto">
                        <option value="">Todas las prioridades</option>
                        <option value="urgente">Urgente</option>
                        <option value="alta">Alta</option>
                        <option value="media">Media</option>
                        <option value="baja">Baja</option>
                    </select>
                    <select id="filtroAsignado" class="form-select form-select-sm" style="width:auto">
                        <option value="">Todos</option>
                        @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre ?? $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if(auth()->user()->puede('proyectos','crear'))
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTarea">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Tarea
                </button>
                @endif
            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table align-middle" id="tablaTareas">
                    <thead>
                        <tr>
                            <th style="width:90px">Prioridad</th>
                            <th>Título</th>
                            <th>Asignado a</th>
                            <th>Fecha Límite</th>
                            <th style="width:140px">Estado</th>
                            <th style="width:80px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proyecto->tareas->sortBy([['prioridad', 'asc'],['orden','asc']]) as $tarea)
                        @php
                            $prioClase = ['urgente'=>'bg-danger','alta'=>'bg-warning text-dark','media'=>'bg-info','baja'=>'bg-secondary'][$tarea->prioridad] ?? 'bg-secondary';
                            $estClase  = ['pendiente'=>'bg-secondary','en_progreso'=>'bg-primary','completada'=>'bg-success','cancelada'=>'bg-dark'][$tarea->estado] ?? 'bg-secondary';
                        @endphp
                        <tr class="tarea-row"
                            data-estado="{{ $tarea->estado }}"
                            data-prioridad="{{ $tarea->prioridad }}"
                            data-asignado="{{ $tarea->asignado_a }}">
                            <td>
                                <span class="badge {{ $prioClase }}">
                                    {{ ucfirst($tarea->prioridad) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $tarea->titulo }}</div>
                                @if($tarea->descripcion)
                                <small class="text-muted">{{ Str::limit($tarea->descripcion, 60) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($tarea->asignado)
                                <span class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                          style="width:26px;height:26px;font-size:11px;background:{{ $proyecto->color ?? '#3a7bd5' }};color:#fff;">
                                        {{ strtoupper(mb_substr($tarea->asignado->nombre ?? $tarea->asignado->name, 0, 1)) }}
                                    </span>
                                    <small>{{ $tarea->asignado->nombre ?? $tarea->asignado->name }}</small>
                                </span>
                                @else
                                <small class="text-muted">Sin asignar</small>
                                @endif
                            </td>
                            <td>
                                @if($tarea->fecha_limite)
                                <small class="{{ $tarea->fecha_limite->isPast() && $tarea->estado !== 'completada' ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    {{ $tarea->fecha_limite->format('d/m/Y') }}
                                </small>
                                @else
                                <small class="text-muted">—</small>
                                @endif
                            </td>
                            <td>
                                <select class="form-select form-select-sm estado-select"
                                        data-tarea-id="{{ $tarea->id }}"
                                        data-puede-editar="{{ auth()->user()->puede('proyectos','editar') || (int)$tarea->asignado_a === (int)auth()->id() ? '1' : '0' }}">
                                    @foreach(['pendiente'=>'Pendiente','en_progreso'=>'En Progreso','completada'=>'Completada','cancelada'=>'Cancelada'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ $tarea->estado === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-end">
                                @if(auth()->user()->puede('proyectos','editar'))
                                <button class="btn btn-sm btn-outline-secondary btn-editar-tarea"
                                        data-tarea="{{ json_encode($tarea) }}"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarTarea"
                                        title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif
                                @if(auth()->user()->puede('proyectos','eliminar'))
                                <form action="{{ route('tareas.destroy', $tarea) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta tarea?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr id="sinTareas">
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay tareas en este proyecto.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== TAB KANBAN ===== --}}
        <div class="tab-pane fade" id="kanban" role="tabpanel">
            <div class="d-flex gap-3 overflow-auto pb-2 align-items-start" id="kanbanBoard">

                @php
                    $columnas = [
                        'pendiente'   => ['label'=>'Pendiente',   'color'=>'secondary', 'icon'=>'bi-circle'],
                        'en_progreso' => ['label'=>'En Progreso', 'color'=>'primary',   'icon'=>'bi-arrow-repeat'],
                        'completada'  => ['label'=>'Completada',  'color'=>'success',   'icon'=>'bi-check-circle'],
                        'cancelada'   => ['label'=>'Cancelada',   'color'=>'dark',      'icon'=>'bi-x-circle'],
                    ];
                @endphp

                @foreach($columnas as $estadoCol => $col)
                @php $tareaCol = $proyecto->tareas->where('estado', $estadoCol)->sortBy('orden'); @endphp
                <div class="kanban-col flex-shrink-0" data-estado="{{ $estadoCol }}"
                     style="width:270px; min-height:100px;">
                    {{-- Cabecera columna --}}
                    <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi {{ $col['icon'] }} text-{{ $col['color'] }}"></i>
                            <span class="fw-semibold small">{{ $col['label'] }}</span>
                            <span class="badge bg-{{ $col['color'] }} rounded-pill">{{ $tareaCol->count() }}</span>
                        </div>
                        @if(auth()->user()->puede('proyectos','crear'))
                        <button class="btn btn-sm btn-link p-0 text-muted btn-nueva-col"
                                data-estado="{{ $estadoCol }}"
                                data-bs-toggle="modal" data-bs-target="#modalTarea"
                                title="Agregar tarea">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        @endif
                    </div>

                    {{-- Cards --}}
                    <div class="kanban-cards d-flex flex-column gap-2"
                         id="col-{{ $estadoCol }}"
                         ondragover="event.preventDefault()"
                         ondrop="kanbanDrop(event, '{{ $estadoCol }}')">

                        @foreach($tareaCol as $tarea)
                        @php
                            $prioClase = ['urgente'=>'bg-danger','alta'=>'bg-warning text-dark','media'=>'bg-info','baja'=>'bg-secondary'][$tarea->prioridad] ?? 'bg-secondary';
                        @endphp
                        <div class="card border-0 shadow-sm kanban-card"
                             draggable="{{ auth()->user()->puede('proyectos','editar') || (int)$tarea->asignado_a === (int)auth()->id() ? 'true' : 'false' }}"
                             data-tarea-id="{{ $tarea->id }}"
                             ondragstart="kanbanDragStart(event)">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex align-items-start justify-content-between gap-1 mb-1">
                                    <span class="badge {{ $prioClase }} mb-1">{{ ucfirst($tarea->prioridad) }}</span>
                                    @if(auth()->user()->puede('proyectos','editar'))
                                    <button class="btn btn-sm btn-link p-0 text-muted btn-editar-tarea"
                                            data-tarea="{{ json_encode($tarea) }}"
                                            data-bs-toggle="modal" data-bs-target="#modalEditarTarea">
                                        <i class="bi bi-pencil fs-6"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="small fw-semibold mb-1">{{ $tarea->titulo }}</div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    @if($tarea->asignado)
                                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                          title="{{ $tarea->asignado->nombre ?? $tarea->asignado->name }}"
                                          style="width:22px;height:22px;font-size:10px;background:{{ $proyecto->color ?? '#3a7bd5' }};color:#fff;">
                                        {{ strtoupper(mb_substr($tarea->asignado->nombre ?? $tarea->asignado->name, 0, 1)) }}
                                    </span>
                                    @else
                                    <span></span>
                                    @endif
                                    @if($tarea->fecha_limite)
                                    <small class="{{ $tarea->fecha_limite->isPast() && $tarea->estado !== 'completada' ? 'text-danger' : 'text-muted' }}">
                                        <i class="bi bi-calendar2 me-1"></i>{{ $tarea->fecha_limite->format('d/m') }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

</div>

{{-- ===== MODAL NUEVA TAREA ===== --}}
@if(auth()->user()->puede('proyectos','crear'))
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('tareas.store', $proyecto) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-square me-2"></i>Nueva Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Prioridad</label>
                            <select name="prioridad" class="form-select" id="nuevaTareaPrioridad">
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="estado" class="form-select" id="nuevaTareaEstado">
                                <option value="pendiente">Pendiente</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold">Asignar a</label>
                            <select name="asignado_a" class="form-select">
                                <option value="">Sin asignar</option>
                                @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->nombre ?? $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold">Fecha Límite</label>
                            <input type="date" name="fecha_limite" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Crear Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ===== MODAL EDITAR TAREA ===== --}}
@if(auth()->user()->puede('proyectos','editar'))
<div class="modal fade" id="modalEditarTarea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditarTarea" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="editTitulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Prioridad</label>
                            <select name="prioridad" id="editPrioridad" class="form-select">
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="estado" id="editEstado" class="form-select">
                                <option value="pendiente">Pendiente</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold">Asignar a</label>
                            <select name="asignado_a" id="editAsignado" class="form-select">
                                <option value="">Sin asignar</option>
                                @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->nombre ?? $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold">Fecha Límite</label>
                            <input type="date" name="fecha_limite" id="editFechaLimite" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
(function () {
    var csrfToken = '{{ csrf_token() }}';

    // ── Filtros tabla ────────────────────────────────────────
    function aplicarFiltros() {
        var estado    = document.getElementById('filtroEstado')?.value    ?? '';
        var prioridad = document.getElementById('filtroPrioridad')?.value ?? '';
        var asignado  = document.getElementById('filtroAsignado')?.value  ?? '';
        var filas     = document.querySelectorAll('.tarea-row');
        var sinTareas = document.getElementById('sinTareas');
        var visibles  = 0;
        filas.forEach(function (tr) {
            var ok = (!estado    || tr.dataset.estado    === estado) &&
                     (!prioridad || tr.dataset.prioridad === prioridad) &&
                     (!asignado  || tr.dataset.asignado  === asignado);
            tr.style.display = ok ? '' : 'none';
            if (ok) visibles++;
        });
        if (sinTareas) sinTareas.style.display = (filas.length === 0 || visibles === 0) ? '' : 'none';
    }
    ['filtroEstado','filtroPrioridad','filtroAsignado'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', aplicarFiltros);
    });

    // ── Cambio de estado inline (select en tabla) ────────────
    document.querySelectorAll('.estado-select').forEach(function (sel) {
        if (sel.dataset.puedeEditar !== '1') {
            sel.disabled = true;
            return;
        }
        sel.addEventListener('change', function () {
            var tareaId = this.dataset.tareaId;
            var nuevo   = this.value;
            fetch('/tareas/' + tareaId + '/estado', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ estado: nuevo }),
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) alert('Error al actualizar estado.');
            })
            .catch(function () { alert('Error de red.'); });
        });
    });

    // ── Botón "+" en columnas Kanban → pre-selecciona estado ─
    document.querySelectorAll('.btn-nueva-col').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var estado = this.dataset.estado;
            var sel = document.getElementById('nuevaTareaEstado');
            if (sel) sel.value = estado;
        });
    });

    // ── Modal editar tarea ────────────────────────────────────
    document.querySelectorAll('.btn-editar-tarea').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var t = JSON.parse(this.dataset.tarea);
            document.getElementById('editTitulo').value     = t.titulo        ?? '';
            document.getElementById('editDescripcion').value= t.descripcion   ?? '';
            document.getElementById('editPrioridad').value  = t.prioridad     ?? 'media';
            document.getElementById('editEstado').value     = t.estado        ?? 'pendiente';
            document.getElementById('editAsignado').value   = t.asignado_a    ?? '';
            document.getElementById('editFechaLimite').value= t.fecha_limite  ?? '';
            document.getElementById('formEditarTarea').action = '/tareas/' + t.id;
        });
    });

    // ── Kanban drag & drop ────────────────────────────────────
    window.kanbanDragStart = function (event) {
        event.dataTransfer.setData('tareaId', event.currentTarget.dataset.tareaId);
    };

    window.kanbanDrop = function (event, nuevoEstado) {
        event.preventDefault();
        var tareaId = event.dataTransfer.getData('tareaId');
        if (!tareaId) return;

        fetch('/tareas/' + tareaId + '/estado', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ estado: nuevoEstado }),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                // Mover la card al DOM de la nueva columna
                var card = document.querySelector('.kanban-card[data-tarea-id="' + tareaId + '"]');
                var destCol = document.getElementById('col-' + nuevoEstado);
                if (card && destCol) {
                    destCol.appendChild(card);
                }
                // Actualizar contadores de columnas
                document.querySelectorAll('.kanban-col').forEach(function (col) {
                    var estado = col.dataset.estado;
                    var count  = col.querySelectorAll('.kanban-card').length;
                    var badge  = col.querySelector('.badge');
                    if (badge) badge.textContent = count;
                });
            } else {
                alert(data.error || 'No tienes permiso para mover esta tarea.');
            }
        })
        .catch(function () { alert('Error de red.'); });
    };
})();
</script>
@endsection
