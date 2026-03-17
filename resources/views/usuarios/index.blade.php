@extends('layouts.app')
@section('title', 'Empleados')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Empleados</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('usuarios.create', ['tipo' => 'con_acceso']) }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Con acceso
        </a>
        <a href="{{ route('usuarios.create', ['tipo' => 'sin_acceso']) }}" class="btn btn-outline-secondary">
            <i class="bi bi-person-plus"></i> Sin acceso
        </a>
    </div>
</div>

{{-- Empleados CON acceso --}}
<h5 class="mb-3"><span class="badge bg-primary">Con acceso al sistema</span></h5>
<div class="card shadow-sm mb-4">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Cargo</th>
                    <th>Horario</th>
                    <th>Días Laborales</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conAcceso as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->telefono ?? '—' }}</td>
                    <td>{{ $usuario->cargo ?? '—' }}</td>
                    <td>{{ $usuario->horario ?? '—' }}</td>
                    <td>{{ $usuario->dias_laborales ?? '—' }}</td>
                    <td>
                        <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline"
                              onsubmit="return confirmarEliminar(event, '{{ $usuario->name }}')">
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
                    <td colspan="7" class="text-center text-muted py-3">Sin empleados con acceso.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Empleados SIN acceso --}}
<h5 class="mb-3"><span class="badge bg-secondary">Sin acceso al sistema</span></h5>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Cargo</th>
                    <th>Horario</th>
                    <th>Días Laborales</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sinAcceso as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->telefono ?? '—' }}</td>
                    <td>{{ $usuario->cargo ?? '—' }}</td>
                    <td>{{ $usuario->horario ?? '—' }}</td>
                    <td>{{ $usuario->dias_laborales ?? '—' }}</td>
                    <td>
                        <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline"
                              onsubmit="return confirmarEliminar(event, '{{ $usuario->name }}')">
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
                    <td colspan="6" class="text-center text-muted py-3">Sin empleados sin acceso.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
function confirmarEliminar(e, nombre) {
    if (!confirm(`⚠️ ¿Estás seguro de eliminar al empleado "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        e.preventDefault();
        return false;
    }
    return true;
}
</script>
@endsection