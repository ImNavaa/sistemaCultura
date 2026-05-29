@extends('layouts.app')

@section('title', 'Proyectos')

@section('content')
<div class="container-fluid py-4">

    {{-- Encabezado --}}
    <div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
        <h4 class="fw-bold mb-0"><i class="bi bi-kanban me-2"></i>Proyectos</h4>
        @if(auth()->user()->puede('proyectos','crear'))
        <a href="{{ route('proyectos.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Proyecto
        </a>
        @endif
    </div>

    {{-- Alertas --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('proyectos.index') }}" class="row g-2 mb-4">
        <div class="col-sm-auto">
            <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="todos" {{ request('estado','todos') === 'todos' ? 'selected' : '' }}>Todos los estados</option>
                <option value="activo"     {{ request('estado') === 'activo'     ? 'selected' : '' }}>Activo</option>
                <option value="pausado"    {{ request('estado') === 'pausado'    ? 'selected' : '' }}>Pausado</option>
                <option value="completado" {{ request('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
                <option value="cancelado"  {{ request('estado') === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>
        <div class="col-sm-auto">
            <div class="input-group input-group-sm">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar proyecto…"
                       value="{{ request('buscar') }}">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        @if(request('buscar') || (request('estado') && request('estado') !== 'todos'))
        <div class="col-sm-auto">
            <a href="{{ route('proyectos.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-lg"></i> Limpiar
            </a>
        </div>
        @endif
    </form>

    {{-- Grid de proyectos --}}
    @if($proyectos->isEmpty())
    <div class="text-center py-5" style="color: var(--text-muted)">
        <i class="bi bi-kanban fs-1 mb-3 d-block opacity-50"></i>
        <p class="mb-0">No hay proyectos que mostrar.</p>
        @if(auth()->user()->puede('proyectos','crear'))
        <a href="{{ route('proyectos.create') }}" class="btn btn-primary mt-3">
            <i class="bi bi-plus-lg me-1"></i>Crear primer proyecto
        </a>
        @endif
    </div>
    @else
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        @foreach($proyectos as $proyecto)
        @php
            $total      = $proyecto->tareas_count;
            $completadas= $proyecto->tareas_completadas_count;
            $progreso   = $total > 0 ? intval(round($completadas / $total * 100)) : 0;
            $estadoBadge = [
                'activo'     => 'success',
                'pausado'    => 'warning text-dark',
                'completado' => 'primary',
                'cancelado'  => 'secondary',
            ][$proyecto->estado] ?? 'secondary';
        @endphp
        <div class="col">
            <div class="card h-100 shadow-sm border-0" style="border-left: 4px solid {{ $proyecto->color ?? '#3a7bd5' }} !important; border-left-width: 4px !important;">
                <div class="card-body d-flex flex-column gap-2">
                    {{-- Color bar + título + estado --}}
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-block flex-shrink-0"
                                  style="width:12px;height:12px;background:{{ $proyecto->color ?? '#3a7bd5' }}"></span>
                            <h6 class="fw-semibold mb-0">{{ $proyecto->titulo }}</h6>
                        </div>
                        <span class="badge bg-{{ $estadoBadge }} text-nowrap">
                            {{ ucfirst($proyecto->estado) }}
                        </span>
                    </div>

                    {{-- Descripción --}}
                    @if($proyecto->descripcion)
                    <p class="small mb-0 text-muted" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                        {{ $proyecto->descripcion }}
                    </p>
                    @endif

                    {{-- Progreso --}}
                    <div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Progreso</span>
                            <span>{{ $completadas }}/{{ $total }} tareas &mdash; {{ $progreso }}%</span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar" role="progressbar"
                                 style="width:{{ $progreso }}%; background:{{ $proyecto->color ?? '#3a7bd5' }}"
                                 aria-valuenow="{{ $progreso }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    {{-- Fecha límite --}}
                    @if($proyecto->fecha_limite)
                    <div class="small" style="color:var(--text-muted)">
                        <i class="bi bi-calendar-event me-1"></i>
                        Límite: {{ $proyecto->fecha_limite->format('d/m/Y') }}
                        @if($proyecto->estado === 'activo' && $proyecto->fecha_limite->isPast())
                        <span class="badge bg-danger ms-1">Vencido</span>
                        @endif
                    </div>
                    @endif

                    {{-- Miembros + acciones --}}
                    <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                        {{-- Avatares miembros --}}
                        <div class="d-flex align-items-center" style="gap:-4px">
                            @foreach($proyecto->miembros->take(5) as $miembro)
                            @php $iniciales = strtoupper(mb_substr($miembro->nombre ?? $miembro->name, 0, 1)); @endphp
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold border border-2 border-white"
                                  title="{{ $miembro->nombre ?? $miembro->name }}"
                                  style="width:28px;height:28px;font-size:11px;background:{{ $proyecto->color ?? '#3a7bd5' }};color:#fff;margin-left:-6px;">
                                {{ $iniciales }}
                            </span>
                            @endforeach
                            @if($proyecto->miembros->count() > 5)
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold border border-2"
                                  style="width:28px;height:28px;font-size:10px;background:var(--bg-card);color:var(--text-muted);margin-left:-6px;">
                                +{{ $proyecto->miembros->count() - 5 }}
                            </span>
                            @endif
                        </div>
                        <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-sm btn-outline-primary">
                            Ver <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
