@extends('layouts.app')

@section('title', 'Detalle - {{ $user->name }}')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-clock"></i> {{ $user->name }}</h2>
    <div>
        <a href="{{ route('tiempo.create') }}?empleado={{ $user->id }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Registro
        </a>
        <a href="{{ route('tiempo.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

{{-- Resumen de saldo --}}
@if($saldo)
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-success text-center">
            <div class="card-body">
                <h3 class="text-success">{{ \App\Services\TiempoService::formatearHoras($saldo->horas_favor) }}</h3>
                <p class="mb-0 text-muted">Total horas a favor</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-danger text-center">
            <div class="card-body">
                <h3 class="text-danger">{{ \App\Services\TiempoService::formatearHoras($saldo->horas_compensadas) }}</h3>
                <p class="mb-0 text-muted">Total compensado</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-primary text-center">
            <div class="card-body">
                <h3 class="{{ $saldo->saldo >= 0 ? 'text-primary' : 'text-danger' }}">
                    {{ $saldo->saldo < 0 ? '-' : '' }}{{ \App\Services\TiempoService::formatearHoras(abs($saldo->saldo)) }}
                </h3>
                <p class="mb-0 text-muted">Saldo actual</p>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Tabla de registros --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Categoría</th>
                    <th>Horas</th>
                    <th>Descripción</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                <tr>
                    <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($registro->tipo)) }}</td>
                    <td>
                        @if($registro->categoria === 'favor')
                            <span class="badge bg-success">A favor</span>
                        @else
                            <span class="badge bg-danger">Compensación</span>
                        @endif
                    </td>
                    <td>{{ \App\Services\TiempoService::formatearHoras($registro->horas) }}</td>
                    <td>{{ $registro->descripcion ?? '—' }}</td>
                    <td>
                        <form action="{{ route('tiempo.destroy', $registro) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este registro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No hay registros aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection