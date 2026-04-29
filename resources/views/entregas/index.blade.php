@extends('layouts.app')
@section('title', 'Vales de Salida')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon navy"><i class="bi bi-box-arrow-right"></i></div>
        <div>
            <h2>Vales de Salida de Almacén</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('almacen.index') }}">Almacén</a></li>
                    <li class="breadcrumb-item active">Vales de Salida</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('entregas.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-circle me-1"></i> Nueva Entrega
    </a>
</div>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon navy"><i class="bi bi-list-ul"></i></div>
        Historial de entregas
        <span class="badge ms-auto badge-navy">{{ $entregas->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
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
                        <span class="badge badge-navy font-monospace" style="font-size:.8rem;">
                            {{ $entrega->folio ?? '—' }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                    <td>
                        @foreach($entrega->detalles as $d)
                            <div class="small">
                                <span class="fw-semibold">{{ $d->articulo->nombre }}</span>
                                <span class="text-muted">— {{ number_format($d->cantidad, 2) }} {{ $d->articulo->unidad }}(s)</span>
                            </div>
                        @endforeach
                    </td>
                    <td class="small text-muted">{{ $entrega->unidad_solicitante ?? '—' }}</td>
                    <td class="fw-semibold small">{{ $entrega->receptor }}</td>
                    <td class="small">{{ $entrega->responsable->name }}</td>
                    <td class="text-center text-nowrap">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('entregas.pdf', $entrega) }}"
                               class="btn btn-action btn-outline-primary" title="Descargar Vale PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <form action="{{ route('entregas.destroy', $entrega) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('¿Cancelar esta entrega y restaurar el stock?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-action btn-outline-danger" title="Cancelar entrega">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-box-arrow-right"></i>
                            <p>No hay vales de salida registrados.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($entregas->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">{{ $entregas->links() }}</div>
    @endif
</div>

@endsection
