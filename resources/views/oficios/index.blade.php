@extends('layouts.app')

@section('title', 'Oficios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text"></i> Oficios</h2>
    <a href="{{ route('oficios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Oficio
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Nombre del Evento</th>
                    <th>Número de Oficio</th>
                    <th>Cobrado</th>
                    <th>Monto</th>
                    <th>Organizador</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($oficios as $oficio)
                <tr>
                    <td>{{ $oficio->id }}</td>
                    <td>{{ $oficio->fecha->format('d/m/Y') }}</td>
                    <td>{{ $oficio->nombre_evento }}</td>
                    <td>{{ $oficio->numero_oficio }}</td>
                    <td>
                        @if($oficio->cobrado)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        {{ $oficio->monto_cobrado ? '$' . number_format($oficio->monto_cobrado, 2) : '—' }}
                    </td>
                    <td>{{ $oficio->organizador }}</td>
                    <td>
                        <a href="{{ route('oficios.show', $oficio) }}" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('oficios.edit', $oficio) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('oficios.destroy', $oficio) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Estás seguro de eliminar este oficio?')">
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
                    <td colspan="8" class="text-center text-muted py-4">No hay oficios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $oficios->links() }}
</div>
@endsection