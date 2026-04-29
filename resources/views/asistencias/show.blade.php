@extends('layouts.app')
@section('title', 'Historial — ' . $user->name)
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon green"><i class="bi bi-person-check"></i></div>
        <div>
            <h2>{{ $user->name }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('asistencias.index') }}">Asistencia</a></li>
                    <li class="breadcrumb-item active">Historial</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('asistencias.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver al tablero
    </a>
</div>

{{-- Resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-sm-4 col-md-2">
        <div class="stat-card">
            <div class="stat-card-icon green"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="stat-card-value">{{ $resumen['a_tiempo'] }}</div>
                <div class="stat-card-label">A tiempo</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-md-2">
        <div class="stat-card">
            <div class="stat-card-icon amber"><i class="bi bi-clock"></i></div>
            <div>
                <div class="stat-card-value">{{ $resumen['tarde'] }}</div>
                <div class="stat-card-label">Tarde</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-md-2">
        <div class="stat-card">
            <div class="stat-card-icon blue"><i class="bi bi-file-earmark-check"></i></div>
            <div>
                <div class="stat-card-value">{{ $resumen['falta_justificada'] }}</div>
                <div class="stat-card-label">F. Justificada</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-md-2">
        <div class="stat-card">
            <div class="stat-card-icon red"><i class="bi bi-x-circle"></i></div>
            <div>
                <div class="stat-card-value">{{ $resumen['falta_injustificada'] }}</div>
                <div class="stat-card-label">F. Injustificada</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-md-2">
        <div class="stat-card">
            <div class="stat-card-icon purple"><i class="bi bi-star"></i></div>
            <div>
                <div class="stat-card-value">{{ $resumen['horas_extra'] }}</div>
                <div class="stat-card-label">Horas extra</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-md-2">
        <div class="stat-card">
            <div class="stat-card-icon navy"><i class="bi bi-calendar-range"></i></div>
            <div>
                <div class="stat-card-value">{{ $asistencias->total() }}</div>
                <div class="stat-card-label">Total registros</div>
            </div>
        </div>
    </div>
</div>

{{-- Historial --}}
<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon green"><i class="bi bi-calendar-check"></i></div>
        Historial de asistencia
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
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
                    <td class="fw-semibold">{{ $a->fecha->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-{{ $etiqueta['color'] }}" style="font-size:.8rem;">
                            {{ $etiqueta['label'] }}
                        </span>
                    </td>
                    <td>{{ $a->hora_entrada ?? '—' }}</td>
                    <td>{{ $a->hora_salida ?? '—' }}</td>
                    <td class="small text-muted">{{ $a->inmueble ?? '—' }}</td>
                    <td class="small text-muted" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $a->observaciones ?? '—' }}
                    </td>
                    <td class="small">{{ $a->registradoPor->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-calendar-x"></i>
                            <p>Sin registros de asistencia.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($asistencias->hasPages())
    <div class="p-3 border-top">{{ $asistencias->links() }}</div>
    @endif
</div>

@endsection
