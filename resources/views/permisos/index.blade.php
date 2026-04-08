@extends('layouts.app')
@section('title', 'Permisos — ' . $usuario->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-shield-lock"></i> Gestionar permisos</h2>
        <p class="text-muted mb-0">
            <i class="bi bi-person"></i> <strong>{{ $usuario->name }}</strong>
            @if($usuario->cargo) — {{ $usuario->cargo }} @endif
        </p>
    </div>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('permisos.update', $usuario) }}">
    @csrf
    @method('PUT')

    {{-- Rol --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <i class="bi bi-person-badge"></i> Rol del usuario
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <select name="rol_id" class="form-select">
                        <option value="">— Sin rol —</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}"
                                {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                                @if($rol->descripcion) — {{ $rol->descripcion }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 text-muted small">
                    Los permisos del rol se aplican a todos los usuarios con ese rol.
                    Los permisos extra (abajo) pueden agregar o quitar accesos individuales.
                </div>
            </div>
        </div>
    </div>

    {{-- Permisos por módulo --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <i class="bi bi-grid"></i> Permisos extra del usuario
            <small class="opacity-75 ms-2">(sobreescriben al rol)</small>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-secondary">
                    <tr>
                        <th style="width:25%">Módulo</th>
                        <th class="text-center">Ver</th>
                        <th class="text-center">Crear</th>
                        <th class="text-center">Editar</th>
                        <th class="text-center">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permisos as $modulo => $accionesModulo)
                    <tr>
                        <td class="fw-semibold text-capitalize">
                            {{ str_replace('_', ' ', $modulo) }}
                        </td>
                        @foreach(['ver','crear','editar','eliminar'] as $accion)
                            @php
                                $permiso = $accionesModulo->firstWhere('accion', $accion);
                            @endphp
                            <td class="text-center">
                                @if($permiso)
                                    @php
                                        $extraEntry   = $permisosExtra->get($permiso->id);
                                        $tieneDelRol  = in_array($permiso->id, $permisosRol);
                                        $checked      = $extraEntry
                                            ? (bool) $extraEntry->pivot->permitido
                                            : $tieneDelRol;
                                    @endphp
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               name="permisos[]"
                                               value="{{ $permiso->id }}"
                                               {{ $checked ? 'checked' : '' }}
                                               title="{{ $tieneDelRol ? 'Del rol' : 'Extra' }}">
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
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar permisos
        </button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
            Cancelar
        </a>
    </div>
</form>

@endsection
