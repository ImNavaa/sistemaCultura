@extends('layouts.app')
@section('title', 'Historial de Entregas')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-arrow-right"></i> Historial de Entregas</h2>
    <a href="{{ route('entregas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Entrega
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Artículos</th>
                    <th>Unidad solicitante</th>
                    <th>Receptor</th>
                    <th>Responsable</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entregas as $entrega)
                <tr>
                    <td>
                        <span class="badge bg-secondary font-monospace">
                            {{ $entrega->folio ?? '—' }}
                        </span>
                    </td>
                    <td>{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                    <td>
                        @foreach($entrega->detalles as $d)
                            <div class="small">
                                <span class="fw-semibold">{{ $d->articulo->nombre }}</span>
                                <span class="text-muted">
                                    — {{ number_format($d->cantidad, 2) }} {{ $d->articulo->unidad }}(s)
                                </span>
                            </div>
                        @endforeach
                    </td>
                    <td class="small">{{ $entrega->unidad_solicitante ?? '—' }}</td>
                    <td>{{ $entrega->receptor }}</td>
                    <td>{{ $entrega->responsable->name }}</td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('entregas.pdf', $entrega) }}"
                           class="btn btn-sm btn-outline-primary" title="Descargar Vale PDF">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                        <form action="{{ route('entregas.destroy', $entrega) }}" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('¿Cancelar esta entrega y restaurar el stock?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Cancelar entrega">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No hay entregas registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $entregas->links() }}</div>
@endsection
