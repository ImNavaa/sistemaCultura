@extends('layouts.app')
@section('title', $almacen->nombre)
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon teal"><i class="bi bi-box-seam"></i></div>
        <div>
            <h2>{{ $almacen->nombre }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('almacen.index') }}">Almacén</a></li>
                    <li class="breadcrumb-item active">{{ $almacen->nombre }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('entregas.create') }}?articulo={{ $almacen->id }}" class="btn btn-navy">
            <i class="bi bi-box-arrow-right me-1"></i> Registrar Entrega
        </a>
        <a href="{{ route('almacen.edit', $almacen) }}" class="btn btn-outline-warning">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
        <a href="{{ route('almacen.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            @php
                $qty = $almacen->cantidad_actual;
                $icoColor = $qty <= 0 ? 'red' : ($qty <= 5 ? 'amber' : 'green');
            @endphp
            <div class="stat-card-icon {{ $icoColor }}">
                <i class="bi bi-boxes"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ $qty }}</div>
                <div class="stat-card-label">{{ ucfirst($almacen->unidad) }}(s) disponibles</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ $entregas->count() }}</div>
                <div class="stat-card-label">Entregas realizadas</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon teal">
                <i class="bi bi-graph-down-arrow"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ number_format($entregas->sum(fn($e) => $e->detalles->where('articulo_id', $almacen->id)->sum('cantidad')), 2) }}</div>
                <div class="stat-card-label">Total entregado</div>
            </div>
        </div>
    </div>
</div>

{{-- Info del artículo --}}
@if($almacen->descripcion)
<div class="data-card mb-4">
    <div class="data-card-header">
        <div class="header-icon navy"><i class="bi bi-info-circle"></i></div>
        Descripción
    </div>
    <div class="p-3" style="font-size:.9rem;color:#555;">{{ $almacen->descripcion }}</div>
</div>
@endif

{{-- Historial de entregas --}}
<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon blue"><i class="bi bi-clock-history"></i></div>
        Historial de entregas
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Receptor</th>
                    <th>Unidad solicitante</th>
                    <th>Responsable</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entregas as $entrega)
                <tr>
                    <td><span class="badge badge-navy font-monospace">{{ $entrega->folio ?? '—' }}</span></td>
                    <td>{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                    <td>{{ $entrega->receptor }}</td>
                    <td class="text-muted small">{{ $entrega->unidad_solicitante ?? '—' }}</td>
                    <td>{{ $entrega->responsable->name }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('entregas.pdf', $entrega) }}"
                               class="btn btn-action btn-outline-primary" title="Descargar PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <form action="{{ route('entregas.destroy', $entrega) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Cancelar esta entrega y restaurar stock?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-action btn-outline-danger" title="Cancelar">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Sin entregas registradas para este artículo.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
