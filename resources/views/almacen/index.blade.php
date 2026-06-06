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
    @if(auth()->user()->puede('almacen','crear'))
    <a href="{{ route('almacen.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Artículo
    </a>
    @endif
</div>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon navy"><i class="bi bi-list-ul"></i></div>
        Artículos registrados
        <span class="badge ms-2" style="background:#e8eaf6;color:var(--navy);">{{ $articulos->count() }}</span>
    </div>

    <div class="list-toolbar">
        <div class="d-flex gap-1">
            <button class="btn-vista active" data-v="tabla" title="Vista tabla"><i class="bi bi-table"></i></button>
            <button class="btn-vista" data-v="tarjetas" title="Vista tarjetas"><i class="bi bi-grid-3x3-gap"></i></button>
        </div>
        <div class="sep"></div>
        <span class="small text-muted">Ordenar:</span>
        <select class="form-select sort-select">
            <option value="nombre">Nombre</option>
            <option value="stock">Stock</option>
            <option value="unidad">Unidad</option>
            <option value="responsable">Responsable</option>
        </select>
        <button class="btn-sortdir" title="Cambiar dirección"><i class="bi bi-sort-down"></i></button>
    </div>

    {{-- Vista tabla --}}
    <div class="view-tabla table-responsive">
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
                @php $qty = $articulo->cantidad_actual; $cls = $qty <= 0 ? 'stock-low' : ($qty <= 5 ? 'stock-warn' : 'stock-ok'); @endphp
                <tr class="sort-row"
                    data-nombre="{{ strtolower($articulo->nombre) }}"
                    data-stock="{{ $qty }}"
                    data-unidad="{{ strtolower($articulo->unidad) }}"
                    data-responsable="{{ strtolower($articulo->responsable->name) }}">
                    <td class="fw-semibold">{{ $articulo->nombre }}</td>
                    <td class="text-muted" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $articulo->descripcion ?? '—' }}</td>
                    <td>
                        <span class="{{ $cls }}">{{ $qty }}
                            @if($qty <= 0)<i class="bi bi-exclamation-circle ms-1"></i>
                            @elseif($qty <= 5)<i class="bi bi-exclamation-triangle ms-1"></i>@endif
                        </span>
                    </td>
                    <td>{{ ucfirst($articulo->unidad) }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:28px;height:28px;border-radius:50%;background:#e8eaf6;color:var(--navy);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($articulo->responsable->name,0,1)) }}
                            </span>
                            <span class="small">{{ $articulo->responsable->name }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('almacen.show', $articulo) }}" class="btn btn-action btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></a>
                            @if(auth()->user()->puede('almacen','editar'))
                            <a href="{{ route('almacen.edit', $articulo) }}" class="btn btn-action btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            @endif
                            @if(auth()->user()->puede('almacen','eliminar'))
                            <form action="{{ route('almacen.destroy', $articulo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este artículo?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-action btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-box-seam"></i><p>No hay artículos registrados.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Vista tarjetas --}}
    <div class="view-tarjetas grid-cards">
        @foreach($articulos as $articulo)
        @php $qty = $articulo->cantidad_actual; $stockColor = $qty <= 0 ? '#ef4444' : ($qty <= 5 ? '#f59e0b' : '#22c55e'); @endphp
        <div class="list-card sort-card"
             data-nombre="{{ strtolower($articulo->nombre) }}"
             data-stock="{{ $qty }}"
             data-unidad="{{ strtolower($articulo->unidad) }}"
             data-responsable="{{ strtolower($articulo->responsable->name) }}">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="card-title">{{ $articulo->nombre }}</div>
                <span class="rounded-pill px-2 py-0 fw-bold flex-shrink-0"
                      style="font-size:.72rem;background:{{ $stockColor }}18;color:{{ $stockColor }};border:1px solid {{ $stockColor }}44">
                    {{ $qty }} {{ $articulo->unidad }}
                </span>
            </div>
            @if($articulo->descripcion)
            <div class="card-meta" style="-webkit-line-clamp:2;overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;">
                {{ $articulo->descripcion }}
            </div>
            @endif
            <div class="card-meta">
                <span style="width:20px;height:20px;border-radius:50%;background:#e8eaf6;color:var(--navy);display:inline-flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;">
                    {{ strtoupper(substr($articulo->responsable->name,0,1)) }}
                </span>
                {{ $articulo->responsable->name }}
            </div>
            <div class="card-actions">
                <a href="{{ route('almacen.show', $articulo) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-eye me-1"></i>Ver</a>
                @if(auth()->user()->puede('almacen','editar'))
                <a href="{{ route('almacen.edit', $articulo) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                @endif
                @if(auth()->user()->puede('almacen','eliminar'))
                <form action="{{ route('almacen.destroy', $articulo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>initListView('almacen', 'nombre', 'asc');</script>
@endsection
