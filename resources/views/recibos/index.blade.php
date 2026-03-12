@extends('layouts.app')

@section('title', 'Recibos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt"></i> Recibos</h2>
    <a href="{{ route('recibos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Recibo
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>N° Recibo</th>
                    <th>Nombre del Evento</th>
                    <th>Concepto</th>
                    <th>Importe</th>
                    <th>Organizador</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recibos as $recibo)
                <tr>
                    <td>{{ $recibo->id }}</td>
                    <td>{{ $recibo->fecha->format('d/m/Y') }}</td>
                    <td>{{ $recibo->numero_recibo ?? '—' }}</td>
                    <td>{{ $recibo->nombre_evento }}</td>
                    <td>{{ Str::limit($recibo->concepto, 50) }}</td>
                    <td>${{ number_format($recibo->importe, 2) }}</td>
                    <td>{{ $recibo->organizador }}</td>
                    <td>
                        <a href="{{ route('recibos.show', $recibo) }}" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('recibos.edit', $recibo) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('recibos.destroy', $recibo) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Estás seguro de eliminar este recibo?')">
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
                    <td colspan="7" class="text-center text-muted py-4">No hay recibos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $recibos->links() }}
</div>
@endsection