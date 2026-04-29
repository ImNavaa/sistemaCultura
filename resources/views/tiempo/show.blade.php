@extends('layouts.app')
@section('title', 'Tiempo — ' . $user->name)
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon purple"><i class="bi bi-person-clock"></i></div>
        <div>
            <h2>{{ $user->name }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tiempo.index') }}">Tiempo</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tiempo.create') }}?empleado={{ $user->id }}" class="btn btn-navy">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Registro
        </a>
        <a href="{{ route('tiempo.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

{{-- Stat cards --}}
@if($saldo)
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon green"><i class="bi bi-plus-circle"></i></div>
            <div>
                <div class="stat-card-value">{{ \App\Services\TiempoService::formatearHoras($saldo->horas_favor) }}</div>
                <div class="stat-card-label">Total horas a favor</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon red"><i class="bi bi-dash-circle"></i></div>
            <div>
                <div class="stat-card-value">{{ \App\Services\TiempoService::formatearHoras($saldo->horas_compensadas) }}</div>
                <div class="stat-card-label">Total compensado</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon {{ $saldo->saldo >= 0 ? 'blue' : 'amber' }}">
                <i class="bi bi-bar-chart"></i>
            </div>
            <div>
                <div class="stat-card-value" style="color:{{ $saldo->saldo >= 0 ? 'var(--navy3)' : '#e65100' }}">
                    {{ $saldo->saldo < 0 ? '-' : '' }}{{ \App\Services\TiempoService::formatearHoras(abs($saldo->saldo)) }}
                </div>
                <div class="stat-card-label">Saldo actual</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Tabla de registros --}}
<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon purple"><i class="bi bi-list-ul"></i></div>
        Registros de tiempo
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Categoría</th>
                    <th>Horas</th>
                    <th>Descripción</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                <tr>
                    <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                    <td class="small">{{ str_replace('_', ' ', ucfirst($registro->tipo)) }}</td>
                    <td>
                        @if($registro->categoria === 'favor')
                            <span class="badge" style="background:#e8f5e9;color:#2e7d32;">
                                <i class="bi bi-arrow-up me-1"></i>A favor
                            </span>
                        @else
                            <span class="badge" style="background:#ffebee;color:#c62828;">
                                <i class="bi bi-arrow-down me-1"></i>Compensación
                            </span>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ \App\Services\TiempoService::formatearHoras($registro->horas) }}</td>
                    <td class="small text-muted">{{ $registro->descripcion ?? '—' }}</td>
                    <td class="text-center">
                        <form action="{{ route('tiempo.destroy', $registro) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este registro?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-action btn-outline-danger" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-clock"></i>
                            <p>No hay registros de tiempo aún.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
