@extends('layouts.app')
@section('title', 'Oficios')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon teal"><i class="bi bi-file-earmark-text"></i></div>
        <div>
            <h2>Oficios</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Oficios</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('oficios.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Oficio
    </a>
</div>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon teal"><i class="bi bi-list-ul"></i></div>
        Registros
        <span class="badge ms-auto" style="background:#e0f2f1;color:#00695c;">{{ $oficios->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Evento</th>
                    <th>No. Oficio</th>
                    <th>Cobrado</th>
                    <th>Monto</th>
                    <th>Organizador</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($oficios as $oficio)
                <tr>
                    <td class="text-muted small">{{ $oficio->fecha->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $oficio->nombre_evento }}</td>
                    <td>
                        <span class="badge" style="background:#e8eaf6;color:var(--navy);font-family:monospace;">
                            {{ $oficio->numero_oficio }}
                        </span>
                    </td>
                    <td>
                        @if($oficio->cobrado)
                            <span class="badge" style="background:#e8f5e9;color:#2e7d32;">
                                <i class="bi bi-check me-1"></i>Sí
                            </span>
                        @else
                            <span class="badge bg-light text-muted border">No</span>
                        @endif
                    </td>
                    <td class="fw-semibold">
                        {{ $oficio->monto_cobrado ? '$' . number_format($oficio->monto_cobrado, 2) : '—' }}
                    </td>
                    <td class="small text-muted">{{ $oficio->organizador }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('oficios.show', $oficio) }}"
                               class="btn btn-action btn-outline-primary" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('oficios.edit', $oficio) }}"
                               class="btn btn-action btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('oficios.destroy', $oficio) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este oficio?')">
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
                            <i class="bi bi-file-earmark-text"></i>
                            <p>No hay oficios registrados.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($oficios->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">{{ $oficios->links() }}</div>
    @endif
</div>

@endsection
