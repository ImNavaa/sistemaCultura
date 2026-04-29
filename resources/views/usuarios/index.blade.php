@extends('layouts.app')
@section('title', 'Empleados')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon blue"><i class="bi bi-people"></i></div>
        <div>
            <h2>Empleados</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Empleados</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('usuarios.create', ['tipo' => 'con_acceso']) }}" class="btn btn-navy">
            <i class="bi bi-person-plus me-1"></i> Con acceso
        </a>
        <a href="{{ route('usuarios.create', ['tipo' => 'sin_acceso']) }}" class="btn btn-outline-secondary">
            <i class="bi bi-person-plus me-1"></i> Sin acceso
        </a>
    </div>
</div>

{{-- Empleados CON acceso --}}
<div class="data-card mb-4">
    <div class="data-card-header">
        <div class="header-icon blue"><i class="bi bi-person-check"></i></div>
        Con acceso al sistema
        <span class="badge ms-2" style="background:#e3f2fd;color:#1565c0;">{{ $conAcceso->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Cargo</th>
                    <th>Horario</th>
                    <th>Días laborales</th>
                    <th>Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conAcceso as $usuario)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($usuario->name, 0, 1)) }}
                            </span>
                            <div>
                                <div class="fw-semibold" style="font-size:.9rem;">{{ $usuario->name }}</div>
                                <div class="text-muted" style="font-size:.78rem;">{{ $usuario->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="small text-muted">{{ $usuario->cargo ?? '—' }}</td>
                    <td class="small">
                        @if($usuario->horario)
                            <i class="bi bi-clock me-1 text-muted"></i>{{ $usuario->horario }}
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($usuario->dias_laborales)
                            @php $dias = explode(',', $usuario->dias_laborales); @endphp
                            @foreach($dias as $dia)
                                <span class="day-chip">{{ trim($dia) }}</span>
                            @endforeach
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        @if($usuario->rol)
                            <span class="badge" style="background:#e8eaf6;color:var(--navy);">
                                {{ ucfirst(str_replace('_', ' ', $usuario->rol->nombre)) }}
                            </span>
                        @else
                            <span class="badge bg-light text-dark border">Sin rol</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('usuarios.show', $usuario) }}"
                               class="btn btn-action btn-outline-primary" title="Ver perfil">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('usuarios.edit', $usuario) }}"
                               class="btn btn-action btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if(auth()->user()->puede('usuarios', 'editar'))
                            <a href="{{ route('permisos.index', $usuario) }}"
                               class="btn btn-action btn-outline-info" title="Gestionar permisos">
                                <i class="bi bi-shield-lock"></i>
                            </a>
                            @endif
                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirmarEliminar(event, {{ Js::from($usuario->name) }})">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-action btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-person-x"></i>
                            <p>Sin empleados con acceso al sistema.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Empleados SIN acceso --}}
<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon" style="background:#9e9e9e;color:#fff;width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.9rem;">
            <i class="bi bi-person"></i>
        </div>
        Sin acceso al sistema
        <span class="badge ms-2 bg-secondary">{{ $sinAcceso->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Cargo</th>
                    <th>Teléfono</th>
                    <th>Horario</th>
                    <th>Días laborales</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sinAcceso as $usuario)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:34px;height:34px;border-radius:50%;background:#e0e0e0;color:#616161;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($usuario->name, 0, 1)) }}
                            </span>
                            <div class="fw-semibold" style="font-size:.9rem;">{{ $usuario->name }}</div>
                        </div>
                    </td>
                    <td class="small text-muted">{{ $usuario->cargo ?? '—' }}</td>
                    <td class="small">{{ $usuario->telefono ?? '—' }}</td>
                    <td class="small">
                        @if($usuario->horario)
                            <i class="bi bi-clock me-1 text-muted"></i>{{ $usuario->horario }}
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($usuario->dias_laborales)
                            @php $dias = explode(',', $usuario->dias_laborales); @endphp
                            @foreach($dias as $dia)
                                <span class="day-chip">{{ trim($dia) }}</span>
                            @endforeach
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('usuarios.show', $usuario) }}"
                               class="btn btn-action btn-outline-primary" title="Ver perfil">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('usuarios.edit', $usuario) }}"
                               class="btn btn-action btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirmarEliminar(event, {{ Js::from($usuario->name) }})">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-action btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-person"></i>
                            <p>Sin empleados sin acceso registrados.</p>
                        </div>
                    </td>
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
    if (!confirm(`¿Estás seguro de eliminar al empleado "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        e.preventDefault();
        return false;
    }
    return true;
}
</script>
@endsection
