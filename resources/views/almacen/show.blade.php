@extends('layouts.app')
@section('title', 'Detalle Artículo')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam"></i> {{ $almacen->nombre }}</h2>
    <div>
        <a href="{{ route('entregas.create') }}?articulo={{ $almacen->id }}" class="btn btn-success">
            <i class="bi bi-box-arrow-right"></i> Registrar Entrega
        </a>
        <a href="{{ route('almacen.edit', $almacen) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('almacen.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center border-success">
            <div class="card-body">
                <h3 class="text-success">{{ $almacen->cantidad_actual }}</h3>
                <p class="mb-0 text-muted">{{ $almacen->unidad }}(s) disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h3>{{ $entregas->count() }}</h3>
                <p class="mb-0 text-muted">Entregas realizadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h3>{{ $entregas->sum('cantidad') }}</h3>
                <p class="mb-0 text-muted">Total entregado</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header"><strong>Historial de entregas</strong></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Receptor</th>
                    <th>Cantidad</th>
                    <th>Responsable</th>
                    <th>Observaciones</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entregas as $entrega)
                <tr>
                    <td>{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                    <td>{{ $entrega->receptor }}</td>
                    <td>{{ $entrega->cantidad }} {{ $almacen->unidad }}</td>
                    <td>{{ $entrega->responsable->name }}</td>
                    <td>{{ $entrega->observaciones ?? '—' }}</td>
                    <td>
                        <form action="{{ route('entregas.destroy', $entrega) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Cancelar esta entrega y restaurar stock?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Sin entregas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection