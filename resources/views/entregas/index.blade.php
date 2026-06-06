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
    @if(auth()->user()->puede('almacen','crear'))
    <a href="{{ route('entregas.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-circle me-1"></i> Nueva Entrega
    </a>
    @endif
</div>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon navy"><i class="bi bi-list-ul"></i></div>
        Historial de entregas
        <span class="badge ms-2 badge-navy">{{ $entregas->total() }}</span>
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
            <option value="folio">Folio</option>
            <option value="receptor">Receptor</option>
            <option value="unidad">Unidad solicitante</option>
        </select>
        <button class="btn-sortdir" title="Cambiar dirección"><i class="bi bi-sort-down"></i></button>
    </div>

    {{-- Vista tabla --}}
    <div class="view-tabla table-responsive">
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
                <tr class="sort-row"
                    data-fecha="{{ $entrega->fecha_entrega->format('Y-m-d') }}"
                    data-folio="{{ $entrega->folio }}"
                    data-receptor="{{ strtolower($entrega->receptor) }}"
                    data-unidad="{{ strtolower($entrega->unidad_solicitante ?? '') }}">
                    <td><span class="badge badge-navy font-monospace" style="font-size:.8rem;">{{ $entrega->folio ?? '—' }}</span></td>
                    <td class="text-muted small">{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                    <td>
                        @foreach($entrega->detalles as $d)
                        <div class="small"><span class="fw-semibold">{{ $d->articulo->nombre }}</span> <span class="text-muted">— {{ number_format($d->cantidad,2) }} {{ $d->articulo->unidad }}(s)</span></div>
                        @endforeach
                    </td>
                    <td class="small text-muted">{{ $entrega->unidad_solicitante ?? '—' }}</td>
                    <td class="fw-semibold small">{{ $entrega->receptor }}</td>
                    <td class="small">{{ $entrega->responsable->name }}</td>
                    <td class="text-center text-nowrap">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('entregas.pdf', $entrega) }}" class="btn btn-action btn-outline-primary" title="PDF"><i class="bi bi-file-earmark-pdf"></i></a>
                            @if(auth()->user()->puede('almacen','eliminar'))
                            <form action="{{ route('entregas.destroy', $entrega) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Cancelar esta entrega y restaurar el stock?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-action btn-outline-danger" title="Cancelar"><i class="bi bi-arrow-counterclockwise"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-box-arrow-right"></i><p>No hay vales de salida registrados.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Vista tarjetas --}}
    <div class="view-tarjetas grid-cards">
        @foreach($entregas as $entrega)
        <div class="list-card sort-card"
             data-fecha="{{ $entrega->fecha_entrega->format('Y-m-d') }}"
             data-folio="{{ $entrega->folio }}"
             data-receptor="{{ strtolower($entrega->receptor) }}"
             data-unidad="{{ strtolower($entrega->unidad_solicitante ?? '') }}">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <span class="badge badge-navy font-monospace" style="font-size:.78rem;">{{ $entrega->folio ?? '—' }}</span>
                <span class="card-meta"><i class="bi bi-calendar2"></i> {{ $entrega->fecha_entrega->format('d/m/Y') }}</span>
            </div>
            <div class="card-title">{{ $entrega->receptor }}</div>
            @if($entrega->unidad_solicitante)
            <div class="card-meta"><i class="bi bi-building"></i> {{ $entrega->unidad_solicitante }}</div>
            @endif
            <div>
                @foreach($entrega->detalles as $d)
                <div class="card-meta"><i class="bi bi-box"></i> {{ $d->articulo->nombre }} <span class="text-muted">× {{ number_format($d->cantidad,2) }}</span></div>
                @endforeach
            </div>
            <div class="card-meta"><i class="bi bi-person"></i> {{ $entrega->responsable->name }}</div>
            <div class="card-actions">
                <a href="{{ route('entregas.pdf', $entrega) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
                @if(auth()->user()->puede('almacen','eliminar'))
                <form action="{{ route('entregas.destroy', $entrega) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Cancelar entrega?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-arrow-counterclockwise"></i></button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if($entregas->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">{{ $entregas->links() }}</div>
    @endif
</div>

<script>initListView('entregas', 'fecha', 'desc');</script>
@endsection
