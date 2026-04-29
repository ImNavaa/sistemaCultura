@extends('layouts.app')
@section('title', 'Recibos')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon amber"><i class="bi bi-receipt"></i></div>
        <div>
            <h2>Recibos</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Recibos</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('recibos.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Recibo
    </a>
</div>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon amber"><i class="bi bi-list-ul"></i></div>
        Registros
        <span class="badge ms-auto" style="background:#fff8e1;color:#e65100;">{{ $recibos->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>N° Recibo</th>
                    <th>Evento</th>
                    <th>Concepto</th>
                    <th>Importe</th>
                    <th>Organizador</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recibos as $recibo)
                <tr>
                    <td class="text-muted small">{{ $recibo->fecha->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge" style="background:#fff8e1;color:#e65100;font-family:monospace;">
                            {{ $recibo->numero_recibo ?? '—' }}
                        </span>
                    </td>
                    <td class="fw-semibold">{{ $recibo->nombre_evento }}</td>
                    <td class="small text-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $recibo->concepto }}
                    </td>
                    <td class="fw-semibold">${{ number_format($recibo->importe, 2) }}</td>
                    <td class="small text-muted">{{ $recibo->organizador }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('recibos.show', $recibo) }}"
                               class="btn btn-action btn-outline-primary" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('recibos.edit', $recibo) }}"
                               class="btn btn-action btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('recibos.destroy', $recibo) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este recibo?')">
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
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-receipt"></i>
                            <p>No hay recibos registrados.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($recibos->hasPages())
    <div class="p-3 border-top">{{ $recibos->links() }}</div>
    @endif
</div>

@endsection
