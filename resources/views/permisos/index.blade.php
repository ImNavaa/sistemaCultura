@extends('layouts.app')
@section('title', 'Permisos — ' . $usuario->name)
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon navy"><i class="bi bi-shield-lock"></i></div>
        <div>
            <h2>Gestionar permisos</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Empleados</a></li>
                    <li class="breadcrumb-item active">Permisos</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

{{-- Usuario info --}}
<div class="data-card mb-4">
    <div class="p-3 d-flex align-items-center gap-3">
        <span style="width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;flex-shrink:0;">
            {{ strtoupper(substr($usuario->name, 0, 1)) }}
        </span>
        <div>
            <div class="fw-bold">{{ $usuario->name }}</div>
            @if($usuario->cargo)
                <div class="text-muted small">{{ $usuario->cargo }}</div>
            @endif
        </div>
    </div>
</div>

<form method="POST" action="{{ route('permisos.update', $usuario) }}">
    @csrf @method('PUT')

    {{-- Rol --}}
    <div class="data-card mb-4">
        <div class="data-card-header">
            <div class="header-icon navy"><i class="bi bi-person-badge"></i></div>
            Rol del usuario
        </div>
        <div class="p-3">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <label class="form-label">Rol asignado</label>
                    <select name="rol_id" class="form-select">
                        <option value="">— Sin rol —</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                                @if($rol->descripcion) — {{ $rol->descripcion }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <div class="p-3 rounded" style="background:#f0f4ff;border:1px solid #c5cae9;font-size:.85rem;color:#555;">
                        <i class="bi bi-info-circle me-1 text-primary"></i>
                        Los permisos del rol se aplican a todos los usuarios con ese rol.
                        Los permisos extra (tabla de abajo) pueden agregar o restringir accesos de forma individual.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Matriz de permisos --}}
    <div class="data-card mb-4">
        <div class="data-card-header">
            <div class="header-icon navy"><i class="bi bi-grid-3x3"></i></div>
            Permisos individuales
            <span class="ms-2 text-muted" style="font-size:.78rem;font-weight:400;">(sobreescriben al rol)</span>
        </div>
        <div class="table-responsive">
            <table class="table perm-table mb-0">
                <thead>
                    <tr>
                        <th style="width:30%">Módulo</th>
                        <th class="text-center"><i class="bi bi-eye me-1"></i>Ver</th>
                        <th class="text-center"><i class="bi bi-plus-circle me-1"></i>Crear</th>
                        <th class="text-center"><i class="bi bi-pencil me-1"></i>Editar</th>
                        <th class="text-center"><i class="bi bi-trash me-1"></i>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permisos as $modulo => $accionesModulo)
                    <tr>
                        <td>
                            <span class="fw-semibold text-capitalize" style="font-size:.9rem;">
                                {{ str_replace('_', ' ', $modulo) }}
                            </span>
                        </td>
                        @foreach(['ver','crear','editar','eliminar'] as $accion)
                            @php
                                $permiso    = $accionesModulo->firstWhere('accion', $accion);
                                $extraEntry = $permisosExtra->get($permiso?->id);
                                $tieneDelRol = in_array($permiso?->id, $permisosRol);
                                $checked = $permiso
                                    ? ($extraEntry ? (bool) $extraEntry->pivot->permitido : $tieneDelRol)
                                    : false;
                            @endphp
                            <td class="text-center">
                                @if($permiso)
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               name="permisos[]"
                                               value="{{ $permiso->id }}"
                                               {{ $checked ? 'checked' : '' }}
                                               title="{{ $tieneDelRol ? 'Del rol' : 'Individual' }}">
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-navy px-4">
            <i class="bi bi-save me-1"></i> Guardar permisos
        </button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
            Cancelar
        </a>
    </div>
</form>

@endsection
