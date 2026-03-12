@extends('layouts.app')

@section('title', 'Detalle del Recibo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt"></i> Detalle del Recibo</h2>
    <div>
        <a href="{{ route('recibos.edit', $recibo) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('recibos.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Fecha</dt>
            <dd class="col-sm-9">{{ $recibo->fecha->format('d/m/Y') }}</dd>

            <dt class="col-sm-3">Número de Recibo</dt>
            <dd class="col-sm-9">{{ $recibo->numero_recibo ?? '—' }}</dd>
            
            <dt class="col-sm-3">Nombre del Evento</dt>
            <dd class="col-sm-9">{{ $recibo->nombre_evento }}</dd>

            <dt class="col-sm-3">Importe</dt>
            <dd class="col-sm-9">${{ number_format($recibo->importe, 2) }}</dd>

            <dt class="col-sm-3">Organizador</dt>
            <dd class="col-sm-9">{{ $recibo->organizador }}</dd>

            <dt class="col-sm-3">Concepto</dt>
            <dd class="col-sm-9">{{ $recibo->concepto }}</dd>
            <dt class="col-sm-3">Documento</dt>
            <dd class="col-sm-9">
                @if($recibo->foto)
                @if(Str::endsWith($recibo->foto, '.pdf'))
                <a href="{{ Storage::url($recibo->foto) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-pdf"></i> Ver PDF
                </a>
                @else
                <a href="{{ Storage::url($recibo->foto) }}" target="_blank">
                    <img src="{{ Storage::url($recibo->foto) }}" style="max-width:300px; border-radius:8px" class="img-thumbnail">
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