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
    <div class="d-flex gap-2">
        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-outline-warning">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Avatar + info básica --}}
    <div class="col-md-4">
        <div class="data-card h-100">
            <div class="data-card-header">
                <div class="header-icon blue"><i class="bi bi-person-circle"></i></div>
                Perfil
            </div>
            <div class="p-4 text-center">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;margin:0 auto 1rem;">
                    {{ strtoupper(substr($usuario->name, 0, 1)) }}
                </div>
                <div class="fw-bold fs-5 mb-1">{{ $usuario->name }}</div>
                @if($usuario->cargo)
                    <div class="text-muted small mb-2">{{ $usuario->cargo }}</div>
                @endif
                @if($usuario->rol)
                    <span class="badge" style="background:#e8eaf6;color:var(--navy);font-size:.8rem;">
                        <i class="bi bi-shield me-1"></i>{{ ucfirst(str_replace('_', ' ', $usuario->rol->nombre)) }}
                    </span>
                @else
                    @if($usuario->tiene_acceso)
                        <span class="badge bg-light text-dark border">Sin rol asignado</span>
                    @else
                        <span class="badge bg-secondary">Sin acceso al sistema</span>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Datos de contacto y trabajo --}}
    <div class="col-md-8">
        <div class="data-card h-100">
            <div class="data-card-header">
                <div class="header-icon navy"><i class="bi bi-card-list"></i></div>
                Información del empleado
            </div>
            <div class="p-3">
                <ul class="info-list">
                    @if($usuario->tiene_acceso)
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
                            @else
                                —
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
@endsection
