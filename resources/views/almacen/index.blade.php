@extends('layouts.app')
@section('title', 'Inventario')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon navy"><i class="bi bi-box-seam"></i></div>
        <div>
            <h2>Inventario del Almacén</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Almacén</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('almacen.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Artículo
    </a>
</div>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon navy"><i class="bi bi-list-ul"></i></div>
        Artículos registrados
        <span class="badge ms-auto" style="background:#e8eaf6;color:var(--navy);">{{ $articulos->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Stock</th>
                    <th>Unidad</th>
                    <th>Responsable</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articulos as $articulo)
                <tr>
                    <td class="fw-semibold">{{ $articulo->nombre }}</td>
                    <td class="text-muted" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $articulo->descripcion ?? '—' }}
                    </td>
                    <td>
                        @php
                            $qty = $articulo->cantidad_actual;
                            $cls = $qty <= 0 ? 'stock-low' : ($qty <= 5 ? 'stock-warn' : 'stock-ok');
                        @endphp
                        <span class="{{ $cls }}">
                            {{ $qty }}
                            @if($qty <= 0)
                                <i class="bi bi-exclamation-circle ms-1"></i>
                            @elseif($qty <= 5)
                                <i class="bi bi-exclamation-triangle ms-1"></i>
                            @endif
                        </span>
                    </td>
                    <td>{{ ucfirst($articulo->unidad) }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:28px;height:28px;border-radius:50%;background:#e8eaf6;color:var(--navy);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($articulo->responsable->name, 0, 1)) }}
                            </span>
                            <span class="small">{{ $articulo->responsable->name }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('almacen.show', $articulo) }}"
                               class="btn btn-action btn-outline-primary" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('almacen.edit', $articulo) }}"
                               class="btn btn-action btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('almacen.destroy', $articulo) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este artículo?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-action btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-box-seam"></i>
                            <p>No hay artículos registrados en el inventario.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
