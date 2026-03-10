@extends('layouts.app')

@section('title', 'Detalle Empleado')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person"></i> {{ $usuario->name }}</h2>
    <div>
        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Nombre</dt>
            <dd class="col-sm-9">{{ $usuario->name }}</dd>

            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9">{{ $usuario->email }}</dd>

            <dt class="col-sm-3">Teléfono</dt>
            <dd class="col-sm-9">{{ $usuario->telefono ?? '—' }}</dd>

            <dt class="col-sm-3">Cargo</dt>
            <dd class="col-sm-9">{{ $usuario->cargo ?? '—' }}</dd>

            <dt class="col-sm-3">Horario</dt>
            <dd class="col-sm-9">{{ $usuario->horario ?? '—' }}</dd>

            <dt class="col-sm-3">Días Laborales</dt>
            <dd class="col-sm-9">{{ $usuario->dias_laborales ?? '—' }}</dd>

            <dt class="col-sm-3">Registrado</dt>
            <dd class="col-sm-9">{{ $usuario->created_at->format('d/m/Y') }}</dd>
        </dl>
    </div>
</div>
@endsection