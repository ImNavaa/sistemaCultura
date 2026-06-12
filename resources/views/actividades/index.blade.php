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
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Actividad</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Inscritos</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($actividades as $act)
                @php
                    [$bgTipo, $iconTipo, $labelTipo] = $tipoBadge[$act->tipo] ?? ['secondary', 'bi-circle', $act->tipo];
                    [$bgEst, $colorEst, $labelEst]   = $estadoBadge[$act->estado] ?? ['#eee', '#333', $act->estado];
                    $cupoLabel = $act->cupo_maximo ? $act->inscritos_count . '/' . $act->cupo_maximo : $act->inscritos_count;
                @endphp
                <tr>
                    <td><span class="small text-muted font-monospace">{{ $act->codigo }}</span></td>
                    <td>
                        <a href="{{ route('actividades.show', $act) }}" class="fw-semibold text-decoration-none" style="color:var(--text-main);">
                            {{ $act->nombre }}
                        </a>
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
                            <div class="progress-bar" style="width:{{ min(100, $act->cupo_maximo > 0 ? round(($act->inscritos_count/$act->cupo_maximo)*100) : 0) }}%;background:var(--navy3);"></div>
                        </div>
                        @endif
                    </td>
                    <td>
                        <span style="background:{{ $bgEst }};color:{{ $colorEst }};border-radius:20px;padding:3px 10px;font-size:.75rem;font-weight:600;">
                            {{ $labelEst }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('actividades.show', $act) }}" class="btn btn-action btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></a>
                            @if(auth()->user()->puede('act_asistentes','editar'))
                            <a href="{{ route('actividades.edit', $act) }}" class="btn btn-action btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            @endif
                            @if(auth()->user()->puede('act_asistentes','eliminar'))
                            <form action="{{ route('actividades.destroy', $act) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar la actividad «{{ $act->nombre }}»?\nSe eliminarán también todas las inscripciones.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-action btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
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

@endsection
