@extends('layouts.app')
@section('title', 'Historial de Entregas')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-arrow-right"></i> Historial de Entregas</h2>
    <a href="{{ route('entregas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Entrega
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Artículo</th>
                    <th>Cantidad</th>
                    <th>Receptor</th>
                    <th>Responsable</th>
                    <th>Observaciones</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entregas as $entrega)
                <tr>
                    <td>{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                    <td>{{ $entrega->articulo->nombre }}</td>
                    <td>{{ $entrega->cantidad }} {{ $entrega->articulo->unidad }}</td>
                    <td>{{ $entrega->receptor }}</td>
                    <td>{{ $entrega->responsable->name }}</td>
                    <td>{{ $entrega->observaciones ?? '—' }}</td>
                    <td>
                        <form action="{{ route('entregas.destroy', $entrega) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Cancelar esta entrega?')">
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
                    <td colspan="7" class="text-center text-muted py-4">No hay entregas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $entregas->links() }}</div>
@endsection