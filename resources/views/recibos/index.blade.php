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
    @if(auth()->user()->puede('recibos','crear'))
    <a href="{{ route('recibos.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Recibo
    </a>
    @endif
</div>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon amber"><i class="bi bi-list-ul"></i></div>
        Registros
        <span class="badge ms-2" style="background:#fff8e1;color:#e65100;">{{ $recibos->total() }}</span>
    </div>

    <div class="list-toolbar">
        <div class="d-flex gap-1">
            <button class="btn-vista active" data-v="tabla" title="Vista tabla"><i class="bi bi-table"></i></button>
            <button class="btn-vista" data-v="tarjetas" title="Vista tarjetas"><i class="bi bi-grid-3x3-gap"></i></button>
        </div>
        <div class="sep"></div>
        <span class="small text-muted">Ordenar:</span>
        <select class="form-select sort-select">
            <option value="fecha">Fecha</option>
            <option value="numero">N° Recibo</option>
            <option value="evento">Evento</option>
            <option value="importe">Importe</option>
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
                <tr class="sort-row"
                    data-fecha="{{ $recibo->fecha->format('Y-m-d') }}"
                    data-numero="{{ $recibo->numero_recibo }}"
                    data-evento="{{ strtolower($recibo->nombre_evento) }}"
                    data-importe="{{ $recibo->importe }}"
                    data-organizador="{{ strtolower($recibo->organizador) }}">
                    <td class="text-muted small">{{ $recibo->fecha->format('d/m/Y') }}</td>
                    <td><span class="badge" style="background:#fff8e1;color:#e65100;font-family:monospace;">{{ $recibo->numero_recibo ?? '—' }}</span></td>
                    <td class="fw-semibold">{{ $recibo->nombre_evento }}</td>
                    <td class="small text-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $recibo->concepto }}</td>
                    <td class="fw-semibold">${{ number_format($recibo->importe,2) }}</td>
                    <td class="small text-muted">{{ $recibo->organizador }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('recibos.show', $recibo) }}" class="btn btn-action btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></a>
                            @if(auth()->user()->puede('recibos','editar'))
                            <a href="{{ route('recibos.edit', $recibo) }}" class="btn btn-action btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            @endif
                            @if(auth()->user()->puede('recibos','eliminar'))
                            <form action="{{ route('recibos.destroy', $recibo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este recibo?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-action btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-receipt"></i><p>No hay recibos registrados.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Vista tarjetas --}}
    <div class="view-tarjetas grid-cards">
        @foreach($recibos as $recibo)
        <div class="list-card sort-card"
             data-fecha="{{ $recibo->fecha->format('Y-m-d') }}"
             data-numero="{{ $recibo->numero_recibo }}"
             data-evento="{{ strtolower($recibo->nombre_evento) }}"
             data-importe="{{ $recibo->importe }}"
             data-organizador="{{ strtolower($recibo->organizador) }}">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="card-title">{{ $recibo->nombre_evento }}</div>
                <span class="badge flex-shrink-0" style="background:#fff8e1;color:#e65100;font-family:monospace;font-size:.72rem;">{{ $recibo->numero_recibo ?? '—' }}</span>
            </div>
            <div class="card-meta">
                <i class="bi bi-calendar2"></i> {{ $recibo->fecha->format('d/m/Y') }}
            </div>
            @if($recibo->concepto)
            <div class="card-meta" style="-webkit-line-clamp:1;overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;">
                <i class="bi bi-text-left"></i> {{ $recibo->concepto }}
            </div>
            @endif
            <div class="card-meta">
                <i class="bi bi-person"></i> {{ $recibo->organizador }}
                <span class="ms-auto fw-semibold" style="color:var(--text-main)">${{ number_format($recibo->importe,2) }}</span>
            </div>
            <div class="card-actions">
                <a href="{{ route('recibos.show', $recibo) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-eye me-1"></i>Ver</a>
                @if(auth()->user()->puede('recibos','editar'))
                <a href="{{ route('recibos.edit', $recibo) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                @endif
                @if(auth()->user()->puede('recibos','eliminar'))
                <form action="{{ route('recibos.destroy', $recibo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if($recibos->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">{{ $recibos->links() }}</div>
    @endif
</div>

<script>initListView('recibos', 'fecha', 'desc');</script>
@endsection
