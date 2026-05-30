@extends('layouts.app')

@section('title', $proyecto->titulo)

@push('styles')
<style>
/* ── Variables de prioridad ── */
.prio-urgente { --prio-color: #ef4444; }
.prio-alta    { --prio-color: #f59e0b; }
.prio-media   { --prio-color: #3b82f6; }
.prio-baja    { --prio-color: #6b7280; }

/* ── Encabezado proyecto ── */
.proyecto-header {
    background: var(--bg-card);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 1px 8px rgba(0,0,0,.08);
    border-left: 5px solid v-color;
    margin-bottom: 1.5rem;
}

/* ── Stats pills ── */
.stat-pill {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .45rem .9rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 600;
    background: var(--bg-card-alt);
    border: 1px solid var(--border-color);
    white-space: nowrap;
}
.stat-pill .dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* ── Tabs mejorados ── */
.proj-tabs {
    border-bottom: 2px solid var(--border-color);
    gap: .25rem;
    margin-bottom: 1.25rem;
}
.proj-tabs .nav-link {
    color: var(--text-muted);
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    padding: .6rem 1.1rem;
    border-radius: 0;
    font-size: .875rem;
    font-weight: 600;
    transition: color .15s, border-color .15s;
    background: transparent;
}
.proj-tabs .nav-link:hover { color: var(--text-main); }
.proj-tabs .nav-link.active {
    color: var(--accent, #3a7bd5);
    border-bottom-color: var(--accent, #3a7bd5);
    background: transparent;
}

/* ── Filtros toolbar ── */
.filtros-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.filtros-bar .form-select {
    min-width: 130px;
    font-size: .8rem;
}

/* ── Tarjeta de tarea (lista) ── */
.tarea-card {
    background: var(--bg-card);
    border-radius: 12px;
    border: 1px solid var(--border-color);
    border-left: 4px solid var(--prio-color, #6b7280);
    padding: .85rem 1rem;
    transition: box-shadow .15s, transform .1s;
    cursor: default;
}
.tarea-card:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,.1);
    transform: translateY(-1px);
}
.tarea-card.estado-completada { opacity: .65; }
.tarea-card.estado-cancelada  { opacity: .5; }

.tarea-titulo { font-weight: 600; font-size: .9rem; line-height: 1.3; }
.tarea-desc   { font-size: .78rem; color: var(--text-muted); margin-top: .15rem;
                overflow: hidden; display: -webkit-box;
                -webkit-line-clamp: 1; -webkit-box-orient: vertical; }

.badge-prio {
    font-size: .68rem;
    padding: .25em .55em;
    border-radius: 6px;
    font-weight: 700;
    letter-spacing: .02em;
    background: color-mix(in srgb, var(--prio-color) 15%, transparent);
    color: var(--prio-color);
    border: 1px solid color-mix(in srgb, var(--prio-color) 30%, transparent);
}

.badge-estado {
    font-size: .7rem;
    padding: .25em .6em;
    border-radius: 6px;
    font-weight: 600;
}

.avatar-sm {
    width: 26px; height: 26px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}

.fecha-chip {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .75rem;
    color: var(--text-muted);
    background: var(--bg-card-alt);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: .18em .5em;
}
.fecha-chip.vencida { color: #ef4444; background: rgba(239,68,68,.08); border-color: rgba(239,68,68,.2); }

/* ── Estado select inline ── */
.estado-select-inline {
    font-size: .75rem;
    padding: .2rem .5rem;
    border-radius: 7px;
    border: 1px solid var(--border-color);
    background: var(--bg-input);
    color: var(--text-main);
    min-width: 115px;
}
.estado-select-inline:disabled {
    opacity: .6;
    cursor: not-allowed;
}

/* ── Kanban ── */
.kanban-wrap {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    align-items: flex-start;
}
.kanban-col-wrap {
    flex-shrink: 0;
    width: 280px;
}
.kanban-col-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .6rem .85rem;
    border-radius: 12px 12px 0 0;
    font-size: .82rem;
    font-weight: 700;
    letter-spacing: .02em;
}
.kanban-col-body {
    background: var(--bg-card-alt);
    border: 1px solid var(--border-color);
    border-top: none;
    border-radius: 0 0 12px 12px;
    min-height: 120px;
    padding: .5rem;
    display: flex;
    flex-column: column;
    gap: .5rem;
}
.kanban-col-body .kanban-cards {
    display: flex;
    flex-direction: column;
    gap: .5rem;
    min-height: 60px;
}

/* Colores de columna Kanban */
.kcol-pendiente   .kanban-col-header { background: rgba(107,114,128,.12); color: var(--text-main); }
.kcol-en_progreso .kanban-col-header { background: rgba(59,130,246,.12);  color: #3b82f6; }
.kcol-completada  .kanban-col-header { background: rgba(34,197,94,.12);   color: #16a34a; }
.kcol-cancelada   .kanban-col-header { background: rgba(15,23,42,.1);     color: var(--text-muted); }

[data-theme="dark"] .kcol-pendiente   .kanban-col-header { background: rgba(107,114,128,.2); color: #d1d5db; }
[data-theme="dark"] .kcol-en_progreso .kanban-col-header { background: rgba(59,130,246,.2);  color: #60a5fa; }
[data-theme="dark"] .kcol-completada  .kanban-col-header { background: rgba(34,197,94,.2);   color: #4ade80; }
[data-theme="dark"] .kcol-cancelada   .kanban-col-header { background: rgba(255,255,255,.05);color: #6b7280; }

.kanban-card-item {
    background: var(--bg-card);
    border-radius: 10px;
    border: 1px solid var(--border-color);
    border-left: 3px solid var(--prio-color, #6b7280);
    padding: .65rem .75rem;
    transition: box-shadow .15s, transform .1s;
}
.kanban-card-item[draggable="true"] { cursor: grab; }
.kanban-card-item[draggable="true"]:active { cursor: grabbing; }
.kanban-card-item:hover { box-shadow: 0 3px 10px rgba(0,0,0,.1); transform: translateY(-1px); }
.kanban-col-body.drag-over { box-shadow: inset 0 0 0 2px #3b82f6; }

/* ── Empty state ── */
.empty-col {
    text-align: center;
    padding: 1.25rem .5rem;
    color: var(--text-muted);
    font-size: .78rem;
    border: 2px dashed var(--border-color);
    border-radius: 8px;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @php
        $total       = $proyecto->tareas->count();
        $completadas = $proyecto->tareas->where('estado','completada')->count();
        $enProgreso  = $proyecto->tareas->where('estado','en_progreso')->count();
        $pendientes  = $proyecto->tareas->where('estado','pendiente')->count();
        $canceladas  = $proyecto->tareas->where('estado','cancelada')->count();
        $progreso    = $total > 0 ? intval(round($completadas / $total * 100)) : 0;
        $colorProyecto = $proyecto->color ?? '#3a7bd5';
        $estadoBadgeMap = [
            'activo'     => ['bg'=>'rgba(34,197,94,.15)',  'color'=>'#16a34a', 'label'=>'Activo'],
            'pausado'    => ['bg'=>'rgba(245,158,11,.15)', 'color'=>'#b45309', 'label'=>'Pausado'],
            'completado' => ['bg'=>'rgba(59,130,246,.15)', 'color'=>'#1d4ed8', 'label'=>'Completado'],
            'cancelado'  => ['bg'=>'rgba(107,114,128,.15)','color'=>'#4b5563', 'label'=>'Cancelado'],
        ];
        $eb = $estadoBadgeMap[$proyecto->estado] ?? $estadoBadgeMap['activo'];
    @endphp

    {{-- ══ ENCABEZADO DEL PROYECTO ══ --}}
    <div class="proyecto-header" style="border-left-color: {{ $colorProyecto }}">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">

            {{-- Info izquierda --}}
            <div style="min-width:0">
                {{-- Breadcrumb --}}
                <nav class="d-flex align-items-center gap-1 mb-2" style="font-size:.8rem; color:var(--text-muted)">
                    <a href="{{ route('proyectos.index') }}" class="text-muted text-decoration-none">
                        <i class="bi bi-kanban me-1"></i>Proyectos
                    </a>
                    <i class="bi bi-chevron-right" style="font-size:.65rem"></i>
                    <span>{{ $proyecto->titulo }}</span>
                </nav>

                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="fw-bold fs-5">{{ $proyecto->titulo }}</span>
                    <span class="px-2 py-1 rounded-pill fw-semibold"
                          style="font-size:.72rem; background:{{ $eb['bg'] }}; color:{{ $eb['color'] }}">
                        {{ $eb['label'] }}
                    </span>
                </div>

                @if($proyecto->descripcion)
                <p class="mb-2" style="font-size:.85rem; color:var(--text-muted); max-width:560px">
                    {{ $proyecto->descripcion }}
                </p>
                @endif

                {{-- Meta info --}}
                <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size:.8rem; color:var(--text-muted)">
                    <span><i class="bi bi-person me-1"></i>{{ $proyecto->creador->name }}</span>
                    @if($proyecto->fecha_inicio)
                    <span><i class="bi bi-calendar-check me-1"></i>Inicio: {{ $proyecto->fecha_inicio->format('d/m/Y') }}</span>
                    @endif
                    @if($proyecto->fecha_limite)
                    <span class="{{ $proyecto->estado === 'activo' && $proyecto->fecha_limite->isPast() ? 'text-danger fw-semibold' : '' }}">
                        <i class="bi bi-calendar-x me-1"></i>Límite: {{ $proyecto->fecha_limite->format('d/m/Y') }}
                        @if($proyecto->estado === 'activo' && $proyecto->fecha_limite->isPast())
                        <span class="ms-1 px-2 py-0 rounded-pill fw-bold" style="font-size:.68rem;background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.25)">Vencido</span>
                        @endif
                    </span>
                    @endif
                    {{-- Miembros --}}
                    @if($proyecto->miembros->count())
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-people me-1"></i>
                        @foreach($proyecto->miembros->take(4) as $m)
                        <span class="avatar-sm" title="{{ $m->name }}"
                              style="background:{{ $colorProyecto }}; margin-left:-4px; border:2px solid var(--bg-card)">
                            {{ strtoupper(mb_substr($m->name,0,1)) }}
                        </span>
                        @endforeach
                        @if($proyecto->miembros->count() > 4)
                        <span class="avatar-sm" style="background:var(--bg-card-alt);color:var(--text-muted);border:2px solid var(--border-color);font-size:9px;margin-left:-4px">
                            +{{ $proyecto->miembros->count()-4 }}
                        </span>
                        @endif
                    </span>
                    @endif
                </div>
            </div>

            {{-- Info derecha: progreso + botones --}}
            <div class="d-flex flex-column align-items-end gap-3" style="min-width:200px">

                {{-- Progreso circular visual --}}
                <div class="text-center">
                    <div class="position-relative d-inline-block mb-1">
                        <svg width="72" height="72" viewBox="0 0 72 72">
                            <circle cx="36" cy="36" r="30" fill="none" stroke="var(--border-color)" stroke-width="6"/>
                            <circle cx="36" cy="36" r="30" fill="none"
                                    stroke="{{ $colorProyecto }}" stroke-width="6"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ round(2*3.14159*30, 2) }}"
                                    stroke-dashoffset="{{ round(2*3.14159*30*(1-$progreso/100), 2) }}"
                                    transform="rotate(-90 36 36)"/>
                            <text x="36" y="40" text-anchor="middle" fill="{{ $colorProyecto }}"
                                  font-size="14" font-weight="700">{{ $progreso }}%</text>
                        </svg>
                    </div>
                    <div style="font-size:.75rem; color:var(--text-muted)">{{ $completadas }}/{{ $total }} tareas</div>
                </div>

                {{-- Botones --}}
                <div class="d-flex gap-2">
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
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats pills --}}
        <div class="d-flex gap-2 flex-wrap mt-3 pt-3" style="border-top:1px solid var(--border-color)">
            <span class="stat-pill">
                <span class="dot" style="background:#6b7280"></span>
                Pendientes <strong>{{ $pendientes }}</strong>
            </span>
            <span class="stat-pill">
                <span class="dot" style="background:#3b82f6"></span>
                En Progreso <strong>{{ $enProgreso }}</strong>
            </span>
            <span class="stat-pill">
                <span class="dot" style="background:#22c55e"></span>
                Completadas <strong>{{ $completadas }}</strong>
            </span>
            @if($canceladas)
            <span class="stat-pill">
                <span class="dot" style="background:#374151"></span>
                Canceladas <strong>{{ $canceladas }}</strong>
            </span>
            @endif
        </div>
    </div>

    {{-- ══ TABS ══ --}}
    <ul class="nav proj-tabs" id="proyectoTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="lista-tab" data-bs-toggle="tab"
                    data-bs-target="#lista" type="button" role="tab">
                <i class="bi bi-list-task me-1"></i>Lista de Tareas
                <span class="ms-1 badge rounded-pill" style="background:var(--bg-card-alt);color:var(--text-muted);font-size:.65rem">{{ $total }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="kanban-tab" data-bs-toggle="tab"
                    data-bs-target="#kanban" type="button" role="tab">
                <i class="bi bi-kanban me-1"></i>Tablero Kanban
            </button>
        </li>
    </ul>

    <div class="tab-content" id="proyectoTabsContent">

        {{-- ══════════ TAB LISTA ══════════ --}}
        <div class="tab-pane fade show active" id="lista" role="tabpanel">

            {{-- Toolbar --}}
            <div class="filtros-bar">
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <select id="filtroEstado" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_progreso">En Progreso</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                    <select id="filtroPrioridad" class="form-select form-select-sm">
                        <option value="">Todas las prioridades</option>
                        <option value="urgente">Urgente</option>
                        <option value="alta">Alta</option>
                        <option value="media">Media</option>
                        <option value="baja">Baja</option>
                    </select>
                    <select id="filtroAsignado" class="form-select form-select-sm">
                        <option value="">Todos los miembros</option>
                        @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if(auth()->user()->puede('proyectos','crear'))
                <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTarea">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Tarea
                </button>
                @endif
            </div>

            {{-- Lista de tarjetas --}}
            <div class="d-flex flex-column gap-2" id="listaTareas">

                @php
                    $prioOrden = ['urgente'=>0,'alta'=>1,'media'=>2,'baja'=>3];
                    $tareasOrdenadas = $proyecto->tareas->sortBy(function($t) use($prioOrden){
                        return [$prioOrden[$t->prioridad] ?? 9, $t->orden];
                    });
                @endphp

                @forelse($tareasOrdenadas as $tarea)
                @php
                    $prioColor = ['urgente'=>'#ef4444','alta'=>'#f59e0b','media'=>'#3b82f6','baja'=>'#6b7280'][$tarea->prioridad] ?? '#6b7280';
                    $estadoConf = [
                        'pendiente'   => ['bg'=>'rgba(107,114,128,.12)', 'color'=>'#6b7280',  'label'=>'Pendiente'],
                        'en_progreso' => ['bg'=>'rgba(59,130,246,.12)',  'color'=>'#3b82f6',  'label'=>'En Progreso'],
                        'completada'  => ['bg'=>'rgba(34,197,94,.12)',   'color'=>'#16a34a',  'label'=>'Completada'],
                        'cancelada'   => ['bg'=>'rgba(107,114,128,.08)', 'color'=>'#9ca3af',  'label'=>'Cancelada'],
                    ][$tarea->estado] ?? ['bg'=>'','color'=>'','label'=>$tarea->estado];
                    $vencida = $tarea->fecha_limite && $tarea->fecha_limite->isPast() && $tarea->estado !== 'completada';
                @endphp
                <div class="tarea-card prio-{{ $tarea->prioridad }} estado-{{ $tarea->estado }} tarea-row"
                     data-estado="{{ $tarea->estado }}"
                     data-prioridad="{{ $tarea->prioridad }}"
                     data-asignado="{{ $tarea->asignado_a }}"
                     style="--prio-color:{{ $prioColor }}">

                    <div class="d-flex align-items-center gap-3 flex-wrap">

                        {{-- Prioridad badge --}}
                        <span class="badge-prio flex-shrink-0" style="--prio-color:{{ $prioColor }}">
                            {{ ucfirst($tarea->prioridad) }}
                        </span>

                        {{-- Título + descripción --}}
                        <div class="flex-grow-1" style="min-width:0">
                            <div class="tarea-titulo {{ $tarea->estado === 'completada' ? 'text-decoration-line-through' : '' }}">
                                {{ $tarea->titulo }}
                            </div>
                            @if($tarea->descripcion)
                            <div class="tarea-desc">{{ $tarea->descripcion }}</div>
                            @endif
                        </div>

                        {{-- Asignado --}}
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            @if($tarea->asignado)
                            <span class="avatar-sm" title="{{ $tarea->asignado->name }}"
                                  style="background:{{ $colorProyecto }}">
                                {{ strtoupper(mb_substr($tarea->asignado->name,0,1)) }}
                            </span>
                            <span style="font-size:.78rem; color:var(--text-muted); max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap">
                                {{ $tarea->asignado->name }}
                            </span>
                            @else
                            <span style="font-size:.75rem; color:var(--text-muted)">
                                <i class="bi bi-person-dash me-1"></i>Sin asignar
                            </span>
                            @endif
                        </div>

                        {{-- Fecha límite --}}
                        @if($tarea->fecha_limite)
                        <span class="fecha-chip flex-shrink-0 {{ $vencida ? 'vencida' : '' }}">
                            <i class="bi bi-calendar2{{ $vencida ? '-x' : '' }}"></i>
                            {{ $tarea->fecha_limite->format('d/m/Y') }}
                        </span>
                        @endif

                        {{-- Estado select --}}
                        <select class="estado-select-inline estado-select flex-shrink-0"
                                data-tarea-id="{{ $tarea->id }}"
                                data-puede-editar="{{ auth()->user()->puede('proyectos','editar') || (int)$tarea->asignado_a === (int)auth()->id() ? '1' : '0' }}"
                                style="border-left:3px solid {{ $estadoConf['color'] }}">
                            @foreach(['pendiente'=>'Pendiente','en_progreso'=>'En Progreso','completada'=>'Completada','cancelada'=>'Cancelada'] as $val=>$lbl)
                            <option value="{{ $val }}" {{ $tarea->estado === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>

                        {{-- Acciones --}}
                        <div class="d-flex gap-1 flex-shrink-0">
                            @if(auth()->user()->puede('proyectos','editar'))
                            <button class="btn btn-sm btn-link p-1 text-muted btn-editar-tarea"
                                    data-tarea="{{ json_encode($tarea) }}"
                                    data-bs-toggle="modal" data-bs-target="#modalEditarTarea"
                                    title="Editar tarea">
                                <i class="bi bi-pencil-square fs-6"></i>
                            </button>
                            @endif
                            @if(auth()->user()->puede('proyectos','eliminar'))
                            <form action="{{ route('tareas.destroy', $tarea) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar esta tarea?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-link p-1 text-muted" title="Eliminar"
                                        style="color:var(--text-muted)!important">
                                    <i class="bi bi-trash fs-6 text-danger opacity-60"></i>
                                </button>
                            </form>
                            @endif
                        </div>

                    </div>
                </div>
                @empty
                <div id="sinTareas" class="text-center py-5" style="color:var(--text-muted)">
                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-30"></i>
                    <p class="mb-0">No hay tareas en este proyecto.</p>
                    @if(auth()->user()->puede('proyectos','crear'))
                    <button class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#modalTarea">
                        <i class="bi bi-plus-lg me-1"></i>Crear primera tarea
                    </button>
                    @endif
                </div>
                @endforelse

                {{-- Empty tras filtros --}}
                <div id="sinResultados" class="text-center py-4" style="display:none; color:var(--text-muted)">
                    <i class="bi bi-funnel fs-3 opacity-30 d-block mb-2"></i>
                    <p class="mb-0 small">Sin tareas con estos filtros.</p>
                </div>
            </div>
        </div>

        {{-- ══════════ TAB KANBAN ══════════ --}}
        <div class="tab-pane fade" id="kanban" role="tabpanel">
            <div class="kanban-wrap">

                @php
                    $columnas = [
                        'pendiente'   => ['label'=>'Pendiente',   'icon'=>'bi-circle',        'dot'=>'#6b7280'],
                        'en_progreso' => ['label'=>'En Progreso', 'icon'=>'bi-arrow-repeat',  'dot'=>'#3b82f6'],
                        'completada'  => ['label'=>'Completada',  'icon'=>'bi-check-circle',  'dot'=>'#22c55e'],
                        'cancelada'   => ['label'=>'Cancelada',   'icon'=>'bi-x-circle',      'dot'=>'#374151'],
                    ];
                    $prioColors = ['urgente'=>'#ef4444','alta'=>'#f59e0b','media'=>'#3b82f6','baja'=>'#6b7280'];
                @endphp

                @foreach($columnas as $estadoCol => $col)
                @php $tareaCol = $proyecto->tareas->where('estado', $estadoCol)->sortBy('orden'); @endphp
                <div class="kanban-col-wrap kcol-{{ $estadoCol }}">

                    <div class="kanban-col-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle" style="width:8px;height:8px;background:{{ $col['dot'] }};flex-shrink:0;display:inline-block"></span>
                            <span>{{ $col['label'] }}</span>
                            <span class="rounded-pill px-2 py-0"
                                  style="font-size:.68rem;background:rgba(0,0,0,.08);color:inherit">
                                {{ $tareaCol->count() }}
                            </span>
                        </div>
                        @if(auth()->user()->puede('proyectos','crear'))
                        <button class="btn btn-sm btn-link p-0 btn-nueva-col"
                                data-estado="{{ $estadoCol }}"
                                data-bs-toggle="modal" data-bs-target="#modalTarea"
                                style="color:inherit; opacity:.7"
                                title="Agregar tarea">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        @endif
                    </div>

                    <div class="kanban-col-body"
                         id="col-{{ $estadoCol }}"
                         data-estado="{{ $estadoCol }}"
                         ondragover="kanbanDragOver(event)"
                         ondragleave="kanbanDragLeave(event)"
                         ondrop="kanbanDrop(event, '{{ $estadoCol }}')">

                        <div class="kanban-cards">
                            @forelse($tareaCol as $tarea)
                            @php $pc = $prioColors[$tarea->prioridad] ?? '#6b7280'; @endphp
                            <div class="kanban-card-item prio-{{ $tarea->prioridad }}"
                                 draggable="{{ auth()->user()->puede('proyectos','editar') || (int)$tarea->asignado_a === (int)auth()->id() ? 'true' : 'false' }}"
                                 data-tarea-id="{{ $tarea->id }}"
                                 style="--prio-color:{{ $pc }}"
                                 ondragstart="kanbanDragStart(event)">

                                <div class="d-flex align-items-center justify-content-between mb-1 gap-1">
                                    <span style="font-size:.68rem;font-weight:700;padding:.2em .55em;border-radius:6px;
                                                 background:color-mix(in srgb, {{ $pc }} 15%, transparent);
                                                 color:{{ $pc }};
                                                 border:1px solid color-mix(in srgb, {{ $pc }} 30%, transparent)">
                                        {{ ucfirst($tarea->prioridad) }}
                                    </span>
                                    @if(auth()->user()->puede('proyectos','editar'))
                                    <button class="btn btn-sm btn-link p-0 text-muted btn-editar-tarea"
                                            data-tarea="{{ json_encode($tarea) }}"
                                            data-bs-toggle="modal" data-bs-target="#modalEditarTarea">
                                        <i class="bi bi-pencil" style="font-size:.75rem"></i>
                                    </button>
                                    @endif
                                </div>

                                <div style="font-size:.83rem;font-weight:600;line-height:1.3;margin-bottom:.4rem">
                                    {{ $tarea->titulo }}
                                </div>

                                @if($tarea->descripcion)
                                <div style="font-size:.72rem;color:var(--text-muted);overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;margin-bottom:.4rem">
                                    {{ $tarea->descripcion }}
                                </div>
                                @endif

                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    @if($tarea->asignado)
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="avatar-sm" style="width:20px;height:20px;font-size:9px;background:{{ $colorProyecto }}"
                                              title="{{ $tarea->asignado->name }}">
                                            {{ strtoupper(mb_substr($tarea->asignado->name,0,1)) }}
                                        </span>
                                        <span style="font-size:.72rem;color:var(--text-muted)">{{ Str::words($tarea->asignado->name,1,'') }}</span>
                                    </div>
                                    @else
                                    <span></span>
                                    @endif

                                    @if($tarea->fecha_limite)
                                    @php $vk = $tarea->fecha_limite->isPast() && $tarea->estado !== 'completada'; @endphp
                                    <span style="font-size:.7rem; {{ $vk ? 'color:#ef4444;font-weight:600' : 'color:var(--text-muted)' }}">
                                        <i class="bi bi-calendar2 me-1"></i>{{ $tarea->fecha_limite->format('d/m') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="empty-col">
                                <i class="bi bi-inbox d-block mb-1 opacity-30" style="font-size:1.2rem"></i>
                                Sin tareas
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

{{-- ══ MODAL NUEVA TAREA ══ --}}
@if(auth()->user()->puede('proyectos','crear'))
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tareas.store', $proyecto) }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <span class="me-2 d-inline-flex align-items-center justify-content-center rounded-circle"
                              style="width:32px;height:32px;background:{{ $colorProyecto }}20">
                            <i class="bi bi-plus-lg" style="color:{{ $colorProyecto }}"></i>
                        </span>
                        Nueva Tarea
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control" placeholder="¿Qué hay que hacer?" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Detalles opcionales…"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Prioridad</label>
                            <select name="prioridad" class="form-select form-select-sm" id="nuevaTareaPrioridad">
                                <option value="baja">🔵 Baja</option>
                                <option value="media" selected>🔷 Media</option>
                                <option value="alta">🟡 Alta</option>
                                <option value="urgente">🔴 Urgente</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Estado inicial</label>
                            <select name="estado" class="form-select form-select-sm" id="nuevaTareaEstado">
                                <option value="pendiente">Pendiente</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-7">
                            <label class="form-label fw-semibold small">Asignar a</label>
                            <select name="asignado_a" class="form-select form-select-sm">
                                <option value="">— Sin asignar —</option>
                                @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold small">Fecha Límite</label>
                            <input type="date" name="fecha_limite" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-check-lg me-1"></i>Crear Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ══ MODAL EDITAR TAREA ══ --}}
@if(auth()->user()->puede('proyectos','editar'))
<div class="modal fade" id="modalEditarTarea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditarTarea" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <span class="me-2 d-inline-flex align-items-center justify-content-center rounded-circle"
                              style="width:32px;height:32px;background:rgba(100,100,100,.12)">
                            <i class="bi bi-pencil-square" style="font-size:.85rem"></i>
                        </span>
                        Editar Tarea
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="editTitulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Descripción</label>
                        <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Prioridad</label>
                            <select name="prioridad" id="editPrioridad" class="form-select form-select-sm">
                                <option value="baja">🔵 Baja</option>
                                <option value="media">🔷 Media</option>
                                <option value="alta">🟡 Alta</option>
                                <option value="urgente">🔴 Urgente</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Estado</label>
                            <select name="estado" id="editEstado" class="form-select form-select-sm">
                                <option value="pendiente">Pendiente</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-7">
                            <label class="form-label fw-semibold small">Asignar a</label>
                            <select name="asignado_a" id="editAsignado" class="form-select form-select-sm">
                                <option value="">— Sin asignar —</option>
                                @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold small">Fecha Límite</label>
                            <input type="date" name="fecha_limite" id="editFechaLimite" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-check-lg me-1"></i>Guardar Cambios
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

    // ── Filtros ──────────────────────────────────────────────
    function aplicarFiltros() {
        var estado    = document.getElementById('filtroEstado')?.value    ?? '';
        var prioridad = document.getElementById('filtroPrioridad')?.value ?? '';
        var asignado  = document.getElementById('filtroAsignado')?.value  ?? '';
        var cards     = document.querySelectorAll('.tarea-row');
        var visibles  = 0;
        cards.forEach(function (c) {
            var ok = (!estado    || c.dataset.estado    === estado) &&
                     (!prioridad || c.dataset.prioridad === prioridad) &&
                     (!asignado  || c.dataset.asignado  === asignado);
            c.style.display = ok ? '' : 'none';
            if (ok) visibles++;
        });
        var sinRes = document.getElementById('sinResultados');
        if (sinRes) sinRes.style.display = (cards.length > 0 && visibles === 0) ? '' : 'none';
    }
    ['filtroEstado','filtroPrioridad','filtroAsignado'].forEach(function(id){
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', aplicarFiltros);
    });

    // ── Estado select inline ─────────────────────────────────
    document.querySelectorAll('.estado-select').forEach(function (sel) {
        if (sel.dataset.puedeEditar !== '1') { sel.disabled = true; return; }
        sel.addEventListener('change', function () {
            var id = this.dataset.tareaId, val = this.value, el = this;
            el.disabled = true;
            fetch('/tareas/' + id + '/estado', {
                method: 'PATCH',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ estado: val }),
            })
            .then(r => r.json())
            .then(function(d) {
                el.disabled = false;
                if (!d.success) { alert('Error al actualizar estado.'); }
                else {
                    // Actualizar la card visualmente
                    var card = el.closest('.tarea-row');
                    if (card) {
                        card.dataset.estado = val;
                        var titulo = card.querySelector('.tarea-titulo');
                        if (titulo) titulo.className = 'tarea-titulo' + (val === 'completada' ? ' text-decoration-line-through' : '');
                        card.classList.toggle('estado-completada', val === 'completada');
                        card.classList.toggle('estado-cancelada',  val === 'cancelada');
                    }
                }
            })
            .catch(function() { el.disabled = false; alert('Error de red.'); });
        });
    });

    // ── Modal nueva tarea: pre-seleccionar estado desde Kanban ─
    document.querySelectorAll('.btn-nueva-col').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var sel = document.getElementById('nuevaTareaEstado');
            if (sel) sel.value = this.dataset.estado;
        });
    });

    // ── Modal editar tarea ────────────────────────────────────
    document.querySelectorAll('.btn-editar-tarea').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var t = JSON.parse(this.dataset.tarea);
            document.getElementById('editTitulo').value      = t.titulo       ?? '';
            document.getElementById('editDescripcion').value = t.descripcion  ?? '';
            document.getElementById('editPrioridad').value   = t.prioridad    ?? 'media';
            document.getElementById('editEstado').value      = t.estado       ?? 'pendiente';
            document.getElementById('editAsignado').value    = t.asignado_a   ?? '';
            document.getElementById('editFechaLimite').value = t.fecha_limite ?? '';
            document.getElementById('formEditarTarea').action = '/tareas/' + t.id;
        });
    });

    // ── Kanban drag & drop ────────────────────────────────────
    window.kanbanDragStart = function(e) {
        e.dataTransfer.setData('tareaId', e.currentTarget.dataset.tareaId);
        e.currentTarget.style.opacity = '.5';
    };
    document.querySelectorAll('.kanban-card-item').forEach(function(c){
        c.addEventListener('dragend', function(){ this.style.opacity = ''; });
    });

    window.kanbanDragOver = function(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    };
    window.kanbanDragLeave = function(e) {
        e.currentTarget.classList.remove('drag-over');
    };
    window.kanbanDrop = function(e, nuevoEstado) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');
        var tareaId = e.dataTransfer.getData('tareaId');
        if (!tareaId) return;
        fetch('/tareas/' + tareaId + '/estado', {
            method: 'PATCH',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ estado: nuevoEstado }),
        })
        .then(r => r.json())
        .then(function(d) {
            if (d.success) {
                var card    = document.querySelector('.kanban-card-item[data-tarea-id="' + tareaId + '"]');
                var destCol = document.querySelector('#col-' + nuevoEstado + ' .kanban-cards');
                if (card && destCol) {
                    // Quitar empty state si existe
                    var emptyEl = destCol.querySelector('.empty-col');
                    if (emptyEl) emptyEl.remove();
                    destCol.appendChild(card);
                    card.style.opacity = '';
                }
                // Actualizar contadores
                document.querySelectorAll('.kanban-col-wrap').forEach(function(col) {
                    var est   = col.dataset.estado || col.querySelector('.kanban-col-body')?.dataset.estado;
                    var body  = col.querySelector('.kanban-cards');
                    var count = body ? body.querySelectorAll('.kanban-card-item').length : 0;
                    var badge = col.querySelector('.kanban-col-header .rounded-pill');
                    if (badge) badge.textContent = count;
                    // Mostrar empty state si quedó vacía
                    if (body && count === 0 && !body.querySelector('.empty-col')) {
                        var em = document.createElement('div');
                        em.className = 'empty-col';
                        em.innerHTML = '<i class="bi bi-inbox d-block mb-1 opacity-30" style="font-size:1.2rem"></i>Sin tareas';
                        body.appendChild(em);
                    }
                });
            } else {
                alert(d.error || 'No tienes permiso para mover esta tarea.');
            }
        })
        .catch(function() { alert('Error de red.'); });
    };
})();
</script>
@endsection
