@extends('layouts.app')

@section('title', 'Editar Empleado')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Editar Empleado</h2>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $usuario->name) }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $usuario->email) }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nueva contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Mínimo 8 caracteres">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Repite la nueva contraseña">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="{{ old('telefono', $usuario->telefono) }}" placeholder="Ej: 492 123 4567">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cargo</label>
                    <input type="text" name="cargo" class="form-control"
                           value="{{ old('cargo', $usuario->cargo) }}" placeholder="Ej: Técnico de sonido">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Horario</label>
                    <input type="text" name="horario" class="form-control"
                           value="{{ old('horario', $usuario->horario) }}" placeholder="Ej: 9:00 - 17:00">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Días Laborales</label>
                    <input type="text" name="dias_laborales" class="form-control"
                           value="{{ old('dias_laborales', $usuario->dias_laborales) }}" placeholder="Ej: Lunes a Viernes">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Empleado
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection