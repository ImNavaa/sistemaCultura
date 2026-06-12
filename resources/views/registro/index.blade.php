@extends('layouts.public')
@section('title', 'Actividades disponibles')

@section('content')

<h2 class="fw-bold mb-1" style="color:#1a237e;">Actividades disponibles</h2>
<p class="text-muted mb-4">Selecciona la actividad en la que deseas inscribirte.</p>

@if($actividades->isEmpty())
    <div class="pub-card text-center py-5">
        <i class="bi bi-calendar-x" style="font-size:3rem;color:#cbd5e1;"></i>
        <p class="text-muted mt-3">No hay actividades abiertas en este momento.<br>Regresa pronto.</p>
    </div>
@else
    <div class="row g-3">
        @foreach($actividades as $act)
        @php
            $lleno = $act->cupo_maximo && $act->inscritos_count >= $act->cupo_maximo;
            $disponibles = $act->cupo_maximo ? ($act->cupo_maximo - $act->inscritos_count) : null;
            $tipoColor = [
                'curso'       => '#4f46e5',
                'taller'      => '#0ea5e9',
                'conferencia' => '#d97706',
                'evento'      => '#16a34a',
                'otro'        => '#6b7280',
            ][$act->tipo] ?? '#6b7280';
        @endphp
        <div class="col-md-6">
            <div class="pub-card h-100 d-flex flex-column" style="border-top:4px solid {{ $tipoColor }};">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <span class="badge rounded-pill" style="background:{{ $tipoColor }}20;color:{{ $tipoColor }};font-size:.75rem;padding:.35em .75em;">
                        {{ ucfirst($act->tipo) }}
                    </span>
                    @if($lleno)
                        <span class="badge bg-warning text-dark">Cupo lleno</span>
                    @else
                        <span class="badge bg-success">Abierto</span>
                    @endif
                </div>

                <h5 class="fw-bold mb-1">{{ $act->nombre }}</h5>

                @if($act->descripcion)
                    <p class="text-muted small mb-2" style="flex:1;">{{ Str::limit($act->descripcion, 120) }}</p>
                @endif

                <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                    @if($act->fecha_inicio)
                    <span><i class="bi bi-calendar3 me-1"></i>
                        {{ \Carbon\Carbon::parse($act->fecha_inicio)->translatedFormat('d \d\e F Y') }}
                        @if($act->fecha_fin && $act->fecha_fin != $act->fecha_inicio)
                            — {{ \Carbon\Carbon::parse($act->fecha_fin)->translatedFormat('d \d\e F Y') }}
                        @endif
                    </span>
                    @endif
                    @if($act->hora_inicio)
                    <span><i class="bi bi-clock me-1"></i>{{ substr($act->hora_inicio, 0, 5) }}</span>
                    @endif
                    @if($act->ubicacion)
                    <span><i class="bi bi-geo-alt me-1"></i>{{ $act->ubicacion }}</span>
                    @endif
                    @if($act->instructor)
                    <span><i class="bi bi-person me-1"></i>{{ $act->instructor }}</span>
                    @endif
                    @if($act->modalidad)
                    <span><i class="bi bi-{{ $act->modalidad === 'presencial' ? 'building' : ($act->modalidad === 'virtual' ? 'camera-video' : 'arrows-fullscreen') }} me-1"></i>
                        {{ ucfirst($act->modalidad) }}
                    </span>
                    @endif
                </div>

                @if($act->cupo_maximo)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Cupo</span>
                        <span>{{ $act->inscritos_count }} / {{ $act->cupo_maximo }}</span>
                    </div>
                    <div class="progress" style="height:6px;">
                        @php $pct = round(($act->inscritos_count / $act->cupo_maximo) * 100) @endphp
                        <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success') }}"
                             style="width:{{ $pct }}%"></div>
                    </div>
                    @if(!$lleno)
                        <div class="text-muted small mt-1">{{ $disponibles }} lugar(es) disponibles</div>
                    @endif
                </div>
                @endif

                <div class="mt-auto">
                    @if($lleno)
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="bi bi-x-circle me-1"></i>Cupo lleno
                        </button>
                    @else
                        <a href="{{ route('registro.form', $act) }}" class="btn w-100 text-white"
                           style="background:{{ $tipoColor }};">
                            <i class="bi bi-pencil-square me-1"></i>Inscribirme
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
