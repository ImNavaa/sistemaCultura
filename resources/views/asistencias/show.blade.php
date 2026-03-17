@extends('layouts.app')
@section('title', 'Historial - {{ $user->name }}')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock-history"></i> {{ $user->name }}</h2>
    <a href="{{ route('asistencias.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver al tablero
    </a>
</div>

{{-- Resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card text-center border-success">
            <div class="card-body py-2">
                <h4 class="text-success mb-0">{{ $resumen['a_tiempo'] }}</h4>
                <small>A tiempo</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center border-warning">
            <div class="card-body py-2">
                <h4 class="text-warning mb-0">{{ $resumen['tarde'] }}</h4>
                <small>Tarde</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center border-info">
            <div class="card-body py-2">
                <h4 class="text-info mb-0">{{ $resumen['falta_justificada'] }}</h4>
                <small>F. Justificada</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center border-danger">
            <div class="card-body py-2">
                <h4 class="text-danger mb-0">{{ $resumen['falta_injustificada'] }}</h4>
                <small>F. Injustificada</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center border-primary">
            <div class="card-body py-2">
                <h4 class="text-primary mb-0">{{ $resumen['horas_extra'] }}</h4>
                <small>Horas extra</small>
            </div>
        </div>
    </div>
</div>

{{-- Historial --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Inmueble</th>
                    <th>Observaciones</th>
                    <th>Registró</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asistencias as $a)
                @php $etiqueta = $a->etiqueta(); @endphp
                <tr>
                    <td>{{ $a->fecha->format('d/m/Y') }}</td>
                    <td><span class="badge bg-{{ $etiqueta['color'] }}">{{ $etiqueta['label'] }}</span></td>
                    <td>{{ $a->hora_entrada ?? '—' }}</td>
                    <td>{{ $a->hora_salida ?? '—' }}</td>
                    <td>{{ $a->inmueble ?? '—' }}</td>
                    <td>{{ $a->observaciones ?? '—' }}</td>
                    <td>{{ $a->registradoPor->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Sin registros.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $asistencias->links() }}</div>

@endsection