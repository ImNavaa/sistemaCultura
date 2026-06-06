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
    @if(auth()->user()->puede('oficios','crear'))
    <a href="{{ route('oficios.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Oficio
    </a>
    @endif
</div>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon teal"><i class="bi bi-list-ul"></i></div>
        Registros
        <span class="badge ms-2" style="background:#e0f2f1;color:#00695c;">{{ $oficios->total() }}</span>
        <span class="ms-auto"></span>
    </div>

    {{-- Toolbar --}}
    <div class="list-toolbar">
        <div class="d-flex gap-1">
            <button class="btn-vista active" data-v="tabla" title="Vista tabla"><i class="bi bi-table"></i></button>
            <button class="btn-vista" data-v="tarjetas" title="Vista tarjetas"><i class="bi bi-grid-3x3-gap"></i></button>
        </div>
        <div class="sep"></div>
        <span class="small text-muted">Ordenar:</span>
        <select class="form-select sort-select">
            <option value="fecha">Fecha</option>
            <option value="evento">Evento</option>
            <option value="numero">N° Oficio</option>
            <option value="monto">Monto</option>
            <option value="organizador">Organizador</option>
        </select>
        <button class="btn-sortdir" title="Cambiar dirección"><i class="bi bi-sort-down"></i></button>
    </div>

    {{-- Vista tabla --}}
    <div class="view-tabla table-responsive">
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
            <tbody id="tbody-oficios">
                @forelse($oficios as $oficio)
                <tr class="sort-row"
                    data-fecha="{{ $oficio->fecha->format('Y-m-d') }}"
                    data-evento="{{ strtolower($oficio->nombre_evento) }}"
                    data-numero="{{ $oficio->numero_oficio }}"
                    data-monto="{{ $oficio->monto_cobrado ?? 0 }}"
                    data-organizador="{{ strtolower($oficio->organizador) }}">
                    <td class="text-muted small">{{ $oficio->fecha->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $oficio->nombre_evento }}</td>
                    <td><span class="badge" style="background:#e8eaf6;color:var(--navy);font-family:monospace;">{{ $oficio->numero_oficio }}</span></td>
                    <td>
                        @if($oficio->cobrado)
                            <span class="badge" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-check me-1"></i>Sí</span>
                        @else
                            <span class="badge bg-light text-muted border">No</span>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $oficio->monto_cobrado ? '$'.number_format($oficio->monto_cobrado,2) : '—' }}</td>
                    <td class="small text-muted">{{ $oficio->organizador }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('oficios.show', $oficio) }}" class="btn btn-action btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></a>
                            @if(auth()->user()->puede('oficios','editar'))
                            <a href="{{ route('oficios.edit', $oficio) }}" class="btn btn-action btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            @endif
                            @if(auth()->user()->puede('oficios','eliminar'))
                            <form action="{{ route('oficios.destroy', $oficio) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este oficio?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-action btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-file-earmark-text"></i><p>No hay oficios registrados.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Vista tarjetas --}}
    <div class="view-tarjetas grid-cards" id="grid-oficios">
        @foreach($oficios as $oficio)
        <div class="list-card sort-card"
             data-fecha="{{ $oficio->fecha->format('Y-m-d') }}"
             data-evento="{{ strtolower($oficio->nombre_evento) }}"
             data-numero="{{ $oficio->numero_oficio }}"
             data-monto="{{ $oficio->monto_cobrado ?? 0 }}"
             data-organizador="{{ strtolower($oficio->organizador) }}">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="card-title">{{ $oficio->nombre_evento }}</div>
                <span class="badge flex-shrink-0" style="background:#e8eaf6;color:var(--navy);font-family:monospace;font-size:.72rem;">{{ $oficio->numero_oficio }}</span>
            </div>
            <div class="card-meta">
                <i class="bi bi-calendar2"></i> {{ $oficio->fecha->format('d/m/Y') }}
                @if($oficio->cobrado)
                <span class="badge ms-1" style="background:#e8f5e9;color:#2e7d32;font-size:.65rem;"><i class="bi bi-check me-1"></i>Cobrado</span>
                @endif
            </div>
            <div class="card-meta">
                <i class="bi bi-person"></i> {{ $oficio->organizador }}
                @if($oficio->monto_cobrado)
                <span class="ms-auto fw-semibold" style="color:var(--text-main)">${{ number_format($oficio->monto_cobrado,2) }}</span>
                @endif
            </div>
            <div class="card-actions">
                <a href="{{ route('oficios.show', $oficio) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-eye me-1"></i>Ver</a>
                @if(auth()->user()->puede('oficios','editar'))
                <a href="{{ route('oficios.edit', $oficio) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                @endif
                @if(auth()->user()->puede('oficios','eliminar'))
                <form action="{{ route('oficios.destroy', $oficio) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if($oficios->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">{{ $oficios->links() }}</div>
    @endif
</div>

<script>
initListView('oficios', 'fecha', 'desc');
</script>
@endsection
