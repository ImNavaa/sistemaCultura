@extends('layouts.app')

@section('title', 'Control de Tiempo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock-history"></i> Control de Tiempo del Personal</h2>
    <a href="{{ route('tiempo.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Registro
    </a>
</div>

<div class="row g-3">
    @foreach($empleados as $empleado)
    @php $saldo = $empleado->saldoTiempo; @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">{{ $empleado->name }}</h5>
                <p class="text-muted mb-1" style="font-size:0.85rem">{{ $empleado->cargo ?? '—' }}</p>

                @if($saldo)
                    <div class="mt-3 d-flex justify-content-between">
                        <div class="text-center">
                            <div class="fw-bold text-success">{{ \App\Services\TiempoService::formatearHoras($saldo->horas_favor) }}</div>
                            <small class="text-muted">A favor</small>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold text-danger">{{ \App\Services\TiempoService::formatearHoras($saldo->horas_compensadas) }}</div>
                            <small class="text-muted">Compensado</small>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold {{ $saldo->saldo >= 0 ? 'text-primary' : 'text-danger' }}">
                                {{ \App\Services\TiempoService::formatearHoras(abs($saldo->saldo)) }}
                            </div>
                            <small class="text-muted">Saldo</small>
                        </div>
                    </div>
                @else
                    <p class="text-muted mt-3 mb-0"><small>Sin registros aún</small></p>
                @endif
            </div>
            <div class="card-footer bg-transparent">
                <a href="{{ route('tiempo.show', $empleado) }}" class="btn btn-sm btn-outline-primary w-100">
                    Ver detalle
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection