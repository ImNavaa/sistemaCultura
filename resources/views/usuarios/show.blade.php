@extends('layouts.app')
@section('title', $usuario->name)
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon blue"><i class="bi bi-person"></i></div>
        <div>
            <h2>{{ $usuario->name }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Empleados</a></li>
                    <li class="breadcrumb-item active">{{ $usuario->name }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if(auth()->user()->puede('usuarios','editar'))
        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
        @if($usuario->tiene_acceso)
        <a href="{{ route('permisos.index', $usuario) }}" class="btn btn-outline-info btn-sm">
            <i class="bi bi-shield-lock me-1"></i> Permisos
        </a>
        @endif
        @endif
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

@php
    $cumpleDias  = $usuario->diasParaCumpleanos();
    $cumpleProx  = $usuario->proximoCumpleanos();
    $esHoyCumple = $cumpleDias === 0;
    $vacDisp     = $vacaciones?->diasDisponibles() ?? 0;
    $econDisp    = $diasEcon?->diasPendientes() ?? 0;
    $pendCount   = $diasPendientes->where('estado','pendiente')->count();
    $saldoHoras  = $saldo?->saldo ?? 0;
@endphp

{{-- ── Fila superior: perfil + stats ── --}}
<div class="row g-3 mb-3">

    {{-- Tarjeta perfil --}}
    <div class="col-md-4">
        <div class="data-card h-100">
            <div class="p-4 text-center">
                {{-- Avatar --}}
                <div style="width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;display:flex;align-items:center;justify-content:center;font-size:2.2rem;font-weight:700;margin:0 auto 1rem;position:relative;">
                    {{ strtoupper(mb_substr($usuario->name, 0, 1)) }}
                    @if($esHoyCumple)
                    <span style="position:absolute;bottom:-4px;right:-4px;font-size:1.4rem;">🎂</span>
                    @endif
                </div>
                <div class="fw-bold fs-5 mb-1">{{ $usuario->name }}</div>
                @if($usuario->cargo)
                    <div class="text-muted small mb-2">{{ $usuario->cargo }}</div>
                @endif

                {{-- Recinto --}}
                @if($usuario->recinto)
                <div class="mb-2">
                    <span class="badge" style="background:#e8eaf6;color:#283593;font-size:.8rem;">
                        <i class="bi bi-building me-1"></i>{{ $usuario->recinto }}
                    </span>
                </div>
                @endif

                {{-- Rol --}}
                @if($usuario->rol)
                    <span class="badge" style="background:#e0f2f1;color:#00695c;font-size:.8rem;">
                        <i class="bi bi-shield me-1"></i>{{ ucfirst(str_replace('_', ' ', $usuario->rol->nombre)) }}
                    </span>
                @elseif($usuario->tiene_acceso)
                    <span class="badge bg-light text-dark border">Sin rol</span>
                @else
                    <span class="badge bg-secondary">Sin acceso</span>
                @endif

                {{-- Cumpleaños --}}
                @if($usuario->fecha_nacimiento)
                <div class="mt-3 p-2 rounded-3" style="background:{{ $esHoyCumple ? '#fff8e1' : 'var(--bg-card-alt)' }};border:1px solid {{ $esHoyCumple ? '#fde68a' : 'var(--border-color)' }};">
                    @if($esHoyCumple)
                    <div class="fw-semibold" style="color:#92400e;font-size:.85rem;">🎂 ¡Hoy es su cumpleaños!</div>
                    @else
                    <div class="small" style="color:var(--text-muted);">
                        <i class="bi bi-cake2 me-1"></i>
                        {{ $usuario->fecha_nacimiento->format('d/m') }}
                        — Edad: {{ $usuario->edad() }} años
                    </div>
                    @if($cumpleDias !== null && $cumpleDias <= 30)
                    <div class="small fw-semibold mt-1" style="color:#6366f1;">
                        Cumpleaños en {{ $cumpleDias }} día(s) ({{ $cumpleProx?->format('d/m/Y') }})
                    </div>
                    @endif
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Datos de contacto --}}
    <div class="col-md-8">
        <div class="data-card h-100">
            <div class="data-card-header">
                <div class="header-icon navy"><i class="bi bi-card-list"></i></div>
                Información laboral
            </div>
            <div class="p-3">
                <ul class="info-list">
                    @if($usuario->tiene_acceso && $usuario->email)
                    <li>
                        <span class="info-key"><i class="bi bi-envelope me-1"></i>Email</span>
                        <span class="info-val">{{ $usuario->email }}</span>
                    </li>
                    @endif
                    <li>
                        <span class="info-key"><i class="bi bi-telephone me-1"></i>Teléfono</span>
                        <span class="info-val">{{ $usuario->telefono ?? '—' }}</span>
                    </li>
                    <li>
                        <span class="info-key"><i class="bi bi-briefcase me-1"></i>Cargo</span>
                        <span class="info-val">{{ $usuario->cargo ?? '—' }}</span>
                    </li>
                    <li>
                        <span class="info-key"><i class="bi bi-building me-1"></i>Recinto</span>
                        <span class="info-val">{{ $usuario->recinto ?? '—' }}</span>
                    </li>
                    @if($usuario->fecha_nacimiento)
                    <li>
                        <span class="info-key"><i class="bi bi-cake2 me-1"></i>Nacimiento</span>
                        <span class="info-val">{{ $usuario->fecha_nacimiento->format('d/m/Y') }} ({{ $usuario->edad() }} años)</span>
                    </li>
                    @endif
                    <li>
                        <span class="info-key"><i class="bi bi-clock me-1"></i>Horario</span>
                        <span class="info-val">{{ $usuario->horario ?? '—' }}</span>
                    </li>
                    <li>
                        <span class="info-key"><i class="bi bi-calendar3 me-1"></i>Días laborales</span>
                        <span class="info-val">
                            @if($usuario->dias_laborales)
                                @foreach(explode(',', $usuario->dias_laborales) as $dia)
                                    <span class="day-chip">{{ trim($dia) }}</span>
                                @endforeach
                            @else —
                            @endif
                        </span>
                    </li>
                    <li>
                        <span class="info-key"><i class="bi bi-calendar-check me-1"></i>Registrado</span>
                        <span class="info-val text-muted">{{ $usuario->created_at->format('d/m/Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ── Stats RH ── --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon {{ $saldoHoras > 0 ? 'amber' : 'green' }}">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ number_format($saldoHoras, 1) }}h</div>
                <div class="stat-card-label">Horas pendientes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon {{ $vacDisp > 0 ? 'indigo' : 'gray' }}">
                <i class="bi bi-umbrella"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ $vacaciones ? $vacDisp : '—' }}</div>
                <div class="stat-card-label">Vacaciones disp. {{ $anio }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon {{ $econDisp > 0 ? 'blue' : 'gray' }}">
                <i class="bi bi-calendar2-check"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ $diasEcon ? $econDisp : '—' }}</div>
                <div class="stat-card-label">Días econ. disp. {{ $anio }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon {{ $pendCount > 0 ? 'red' : 'green' }}">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ $pendCount }}</div>
                <div class="stat-card-label">Días pendientes</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Tabs detalle ── --}}
<div class="data-card">
    <ul class="nav nav-tabs px-3 pt-2" id="rhTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tabHoras">
                <i class="bi bi-clock me-1"></i>Horas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabVacaciones">
                <i class="bi bi-umbrella me-1"></i>Vacaciones
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabEconomicos">
                <i class="bi bi-calendar2-check me-1"></i>Días Econ.
            </a>
        </li>
        @if($diasPendientes->isNotEmpty())
        <li class="nav-item">
            <a class="nav-link {{ $pendCount > 0 ? 'text-danger' : '' }}" data-bs-toggle="tab" href="#tabPendientes">
                <i class="bi bi-hourglass-split me-1"></i>Días Pend.
                @if($pendCount > 0)
                <span class="badge bg-danger ms-1" style="font-size:.65rem;">{{ $pendCount }}</span>
                @endif
            </a>
        </li>
        @endif
    </ul>

    <div class="tab-content p-3">

        {{-- ── Tab Horas ── --}}
        <div class="tab-pane fade show active" id="tabHoras">
            @if($saldo)
            <div class="row g-2 mb-3">
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background:#e8f5e9;border:1px solid #a5d6a7;">
                        <div class="fw-bold fs-5" style="color:#2e7d32;">{{ number_format($saldo->horas_favor, 1) }}h</div>
                        <div class="small text-muted">A favor</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background:#fce4ec;border:1px solid #f48fb1;">
                        <div class="fw-bold fs-5" style="color:#c62828;">{{ number_format($saldo->horas_compensadas, 1) }}h</div>
                        <div class="small text-muted">Compensadas</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background:{{ $saldoHoras > 0 ? '#fff8e1' : '#e8f5e9' }};border:1px solid {{ $saldoHoras > 0 ? '#ffe082' : '#a5d6a7' }};">
                        <div class="fw-bold fs-5" style="color:{{ $saldoHoras > 0 ? '#f57f17' : '#2e7d32' }};">{{ number_format($saldoHoras, 1) }}h</div>
                        <div class="small text-muted">Saldo actual</div>
                    </div>
                </div>
            </div>
            @endif
            @if($ultimosRegistros->isNotEmpty())
            <div class="small fw-semibold mb-2" style="color:var(--text-muted);">Últimos movimientos</div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Fecha</th><th>Tipo</th><th>Categoría</th><th class="text-end">Horas</th></tr></thead>
                    <tbody>
                        @foreach($ultimosRegistros as $r)
                        <tr>
                            <td class="small">{{ $r->fecha->format('d/m/Y') }}</td>
                            <td class="small">{{ ucfirst(str_replace('_',' ',$r->tipo)) }}</td>
                            <td>
                                <span class="badge" style="font-size:.7rem;background:{{ $r->categoria==='favor'?'#e8f5e9':'#fce4ec' }};color:{{ $r->categoria==='favor'?'#2e7d32':'#c62828' }};">
                                    {{ $r->categoria === 'favor' ? '+' : '−' }} {{ $r->categoria }}
                                </span>
                            </td>
                            <td class="text-end small fw-semibold" style="color:{{ $r->categoria==='favor'?'#2e7d32':'#c62828' }};">
                                {{ $r->categoria==='favor'?'+':'-' }}{{ number_format($r->horas,1) }}h
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <a href="{{ route('tiempo.show', $usuario) }}" class="btn btn-sm btn-outline-secondary mt-1">
                <i class="bi bi-arrow-right me-1"></i>Ver historial completo
            </a>
            @else
            <div class="empty-state"><i class="bi bi-clock"></i><p>Sin registros de tiempo.</p></div>
            @endif
        </div>

        {{-- ── Tab Vacaciones ── --}}
        <div class="tab-pane fade" id="tabVacaciones">
            @if($vacaciones)
            @php $pct = $vacaciones->dias_asignados > 0 ? intval($vacaciones->dias_usados / $vacaciones->dias_asignados * 100) : 0; @endphp
            <div class="row g-2 mb-3">
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background:#e0e7ff;border:1px solid #a5b4fc;">
                        <div class="fw-bold fs-5" style="color:#3730a3;">{{ $vacaciones->dias_asignados }}</div>
                        <div class="small text-muted">Asignados</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background:#fce4ec;border:1px solid #f48fb1;">
                        <div class="fw-bold fs-5" style="color:#c62828;">{{ $vacaciones->dias_usados }}</div>
                        <div class="small text-muted">Usados</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background:#e8f5e9;border:1px solid #a5d6a7;">
                        <div class="fw-bold fs-5" style="color:#2e7d32;">{{ $vacDisp }}</div>
                        <div class="small text-muted">Disponibles</div>
                    </div>
                </div>
            </div>
            <div class="mb-1 small text-muted">Progreso {{ $anio }}</div>
            <div class="progress mb-3" style="height:10px;border-radius:5px;">
                <div class="progress-bar" style="width:{{ $pct }}%;background:#6366f1;border-radius:5px;"></div>
            </div>
            @if($vacaciones->observaciones)
            <div class="small text-muted p-2 rounded-3" style="background:var(--bg-card-alt);">
                <i class="bi bi-chat-text me-1"></i> {{ $vacaciones->observaciones }}
            </div>
            @endif
            @else
            <div class="empty-state"><i class="bi bi-umbrella"></i><p>Sin vacaciones asignadas para {{ $anio }}.</p></div>
            @endif
            <a href="{{ route('vacaciones.index') }}?anio={{ $anio }}" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-arrow-right me-1"></i>Gestionar vacaciones
            </a>
        </div>

        {{-- ── Tab Días Económicos ── --}}
        <div class="tab-pane fade" id="tabEconomicos">
            @if($diasEcon)
            @php $pctE = $diasEcon->dias_asignados > 0 ? intval($diasEcon->dias_usados / $diasEcon->dias_asignados * 100) : 0; @endphp
            <div class="row g-2 mb-3">
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background:#e3f2fd;border:1px solid #90caf9;">
                        <div class="fw-bold fs-5" style="color:#1565c0;">{{ $diasEcon->dias_asignados }}</div>
                        <div class="small text-muted">Asignados</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background:#fce4ec;border:1px solid #f48fb1;">
                        <div class="fw-bold fs-5" style="color:#c62828;">{{ $diasEcon->dias_usados }}</div>
                        <div class="small text-muted">Usados</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background:#e8f5e9;border:1px solid #a5d6a7;">
                        <div class="fw-bold fs-5" style="color:#2e7d32;">{{ $econDisp }}</div>
                        <div class="small text-muted">Disponibles</div>
                    </div>
                </div>
            </div>
            <div class="progress" style="height:10px;border-radius:5px;">
                <div class="progress-bar" style="width:{{ $pctE }}%;background:#1565c0;border-radius:5px;"></div>
            </div>
            @else
            <div class="empty-state"><i class="bi bi-calendar2-check"></i><p>Sin días económicos asignados para {{ $anio }}.</p></div>
            @endif
            <a href="{{ route('dias-economicos.index') }}" class="btn btn-sm btn-outline-primary mt-3">
                <i class="bi bi-arrow-right me-1"></i>Gestionar días económicos
            </a>
        </div>

        {{-- ── Tab Días Pendientes ── --}}
        @if($diasPendientes->isNotEmpty())
        <div class="tab-pane fade" id="tabPendientes">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Fecha</th><th>Motivo</th><th class="text-center">Estado</th><th>Uso</th></tr></thead>
                    <tbody>
                        @foreach($diasPendientes as $d)
                        <tr>
                            <td class="small">{{ $d->fecha_generacion->format('d/m/Y') }}</td>
                            <td style="font-size:.85rem;">{{ $d->motivo }}</td>
                            <td class="text-center">
                                @if($d->isPendiente())
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;font-size:.7rem;">Pendiente</span>
                                @else
                                <span class="badge" style="background:#dcfce7;color:#15803d;font-size:.7rem;">Utilizado</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $d->fecha_uso?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <a href="{{ route('dias-pendientes.index') }}?empleado={{ $usuario->id }}" class="btn btn-sm btn-outline-secondary mt-1">
                <i class="bi bi-arrow-right me-1"></i>Ver y gestionar
            </a>
        </div>
        @endif

    </div>
</div>

@endsection
