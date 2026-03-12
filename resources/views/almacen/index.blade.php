@extends('layouts.app')
@section('title', 'Almacén')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam"></i> Inventario del Almacén</h2>
    <a href="{{ route('almacen.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Artículo
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Responsable</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articulos as $articulo)
                <tr>
                    <td>{{ $articulo->id }}</td>
                    <td>{{ $articulo->nombre }}</td>
                    <td>{{ $articulo->descripcion ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $articulo->cantidad_actual <= 5 ? 'bg-danger' : 'bg-success' }}">
                            {{ $articulo->cantidad_actual }}
                        </span>
                    </td>
                    <td>{{ $articulo->unidad }}</td>
                    <td>{{ $articulo->responsable->name }}</td>
                    <td>
                        <a href="{{ route('almacen.show', $articulo) }}" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('almacen.edit', $articulo) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('almacen.destroy', $articulo) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este artículo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No hay artículos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection