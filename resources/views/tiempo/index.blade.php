@extends('layouts.app')
@section('title', 'Control de Tiempo')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon purple"><i class="bi bi-clock-history"></i></div>
        <div>
            <h2>Control de Tiempo del Personal</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Tiempo</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('tiempo.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Registro
    </a>
</div>

@if($empleados->isEmpty())
    <div class="data-card">
        <div class="empty-state py-5">
            <i class="bi bi-clock"></i>
            <p>No hay empleados con registros de tiempo.</p>
        </div>
    </div>
@else
<div class="row g-3">
    @foreach($empleados as $empleado)
    @php $saldo = $empleado->saldoTiempo; @endphp
    <div class="col-md-6 col-lg-4">
        <div class="data-card h-100" style="display:flex;flex-direction:column;">
            <div class="data-card-header">
                <span style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;flex-shrink:0;">
                    {{ strtoupper(substr($empleado->name, 0, 1)) }}
                </span>
                <div style="flex:1;min-width:0;">
                    <div class="fw-semibold" style="font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $empleado->name }}</div>
                    @if($empleado->cargo)
                        <div class="text-muted" style="font-size:.75rem;">{{ $empleado->cargo }}</div>
                    @endif
                </div>
            </div>
            <div class="p-3 flex-grow-1">
                @if($saldo)
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="p-2 rounded" style="background:#e8f5e9;">
                                <div class="fw-bold text-success" style="font-size:1.1rem;">
                                    {{ \App\Services\TiempoService::formatearHoras($saldo->horas_favor) }}
                                </div>
                                <div style="font-size:.72rem;color:#666;">A favor</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded" style="background:#ffebee;">
                                <div class="fw-bold text-danger" style="font-size:1.1rem;">
                                    {{ \App\Services\TiempoService::formatearHoras($saldo->horas_compensadas) }}
                                </div>
                                <div style="font-size:.72rem;color:#666;">Compensado</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded" style="background:{{ $saldo->saldo >= 0 ? '#e3f2fd' : '#fff3e0' }};">
                                <div class="fw-bold {{ $saldo->saldo >= 0 ? 'text-primary' : 'text-warning' }}" style="font-size:1.1rem;">
                                    {{ $saldo->saldo < 0 ? '-' : '' }}{{ \App\Services\TiempoService::formatearHoras(abs($saldo->saldo)) }}
                                </div>
                                <div style="font-size:.72rem;color:#666;">Saldo</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-3" style="font-size:.85rem;">
                        <i class="bi bi-inbox d-block mb-1" style="font-size:1.4rem;"></i>
                        Sin registros aún
                    </div>
                @endif
            </div>
            <div class="px-3 pb-3">
                <a href="{{ route('tiempo.show', $empleado) }}" class="btn btn-outline-primary w-100 btn-sm">
                    <i class="bi bi-eye me-1"></i> Ver detalle
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
