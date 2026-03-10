@extends('layouts.app')

@section('title', 'Detalle del Oficio')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text"></i> Detalle del Oficio</h2>
    <div>
        <a href="{{ route('oficios.edit', $oficio) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('oficios.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Fecha</dt>
            <dd class="col-sm-9">{{ $oficio->fecha->format('d/m/Y') }}</dd>

            <dt class="col-sm-3">Hora Inicio</dt>
            <dd class="col-sm-9">{{ $oficio->hora_inicio ?? '—' }}</dd>

            <dt class="col-sm-3">Hora Fin</dt>
            <dd class="col-sm-9">{{ $oficio->hora_fin ?? '—' }}</dd>

            <dt class="col-sm-3">Nombre del Evento</dt>
            <dd class="col-sm-9">{{ $oficio->nombre_evento }}</dd>

            <dt class="col-sm-3">Número de Oficio</dt>
            <dd class="col-sm-9">{{ $oficio->numero_oficio }}</dd>

            <dt class="col-sm-3">Organizador</dt>
            <dd class="col-sm-9">{{ $oficio->organizador }}</dd>

            <dt class="col-sm-3">Cobrado</dt>
            <dd class="col-sm-9">
                @if($oficio->cobrado)
                <span class="badge bg-success">Sí</span>
                @else
                <span class="badge bg-secondary">No</span>
                @endif
            </dd>

            <dt class="col-sm-3">Monto Cobrado</dt>
            <dd class="col-sm-9">
                {{ $oficio->monto_cobrado ? '$' . number_format($oficio->monto_cobrado, 2) : '—' }}
            </dd>
            <dt class="col-sm-3">Documento</dt>
            <dd class="col-sm-9">
                @if($oficio->foto)
                @if(Str::endsWith($oficio->foto, '.pdf'))
                <a href="{{ Storage::url($oficio->foto) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-pdf"></i> Ver PDF
                </a>
                @else
                <a href="{{ Storage::url($oficio->foto) }}" target="_blank">
                    <img src="{{ Storage::url($oficio->foto) }}" style="max-width:300px; border-radius:8px" class="img-thumbnail">
                </a>
                @endif
                @else
                <span class="text-muted">Sin documento</span>
                @endif
            </dd>
        </dl>
    </div>
</div>
@endsection