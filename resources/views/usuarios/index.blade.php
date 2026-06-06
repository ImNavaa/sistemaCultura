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
    @if(auth()->user()->puede('usuarios','crear'))
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('usuarios.create', ['tipo'=>'con_acceso']) }}" class="btn btn-navy"><i class="bi bi-person-plus me-1"></i> Con acceso</a>
        <a href="{{ route('usuarios.create', ['tipo'=>'sin_acceso']) }}" class="btn btn-outline-secondary"><i class="bi bi-person-plus me-1"></i> Sin acceso</a>
    </div>
    @endif
</div>

{{-- ── Con acceso ── --}}
<div class="data-card mb-4">
    <div class="data-card-header">
        <div class="header-icon blue"><i class="bi bi-person-check"></i></div>
        Con acceso al sistema
        <span class="badge ms-2" style="background:#e3f2fd;color:#1565c0;">{{ $conAcceso->count() }}</span>
    </div>

    <div class="list-toolbar">
        <div class="d-flex gap-1">
            <button class="btn-vista active" data-v="tabla" title="Vista tabla"><i class="bi bi-table"></i></button>
            <button class="btn-vista" data-v="tarjetas" title="Vista tarjetas"><i class="bi bi-grid-3x3-gap"></i></button>
        </div>
        <div class="sep"></div>
        <span class="small text-muted">Ordenar:</span>
        <select class="form-select sort-select">
            <option value="nombre">Nombre</option>
            <option value="cargo">Cargo</option>
            <option value="rol">Rol</option>
        </select>
        <button class="btn-sortdir" title="Cambiar dirección"><i class="bi bi-sort-down"></i></button>
    </div>

    {{-- Tabla --}}
    <div class="view-tabla table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Empleado</th><th>Cargo</th><th>Horario</th><th>Días laborales</th><th>Rol</th><th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conAcceso as $usuario)
                <tr class="sort-row"
                    data-nombre="{{ strtolower($usuario->name) }}"
                    data-cargo="{{ strtolower($usuario->cargo ?? '') }}"
                    data-rol="{{ strtolower($usuario->rol->nombre ?? '') }}">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($usuario->name,0,1)) }}</span>
                            <div>
                                <div class="fw-semibold" style="font-size:.9rem;">{{ $usuario->name }}</div>
                                <div class="text-muted" style="font-size:.78rem;">{{ $usuario->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="small text-muted">{{ $usuario->cargo ?? '—' }}</td>
                    <td class="small">@if($usuario->horario)<i class="bi bi-clock me-1 text-muted"></i>{{ $usuario->horario }}@else —@endif</td>
                    <td>
                        @if($usuario->dias_laborales)
                            @foreach(explode(',',$usuario->dias_laborales) as $dia)<span class="day-chip">{{ trim($dia) }}</span>@endforeach
                        @else <span class="text-muted small">—</span>@endif
                    </td>
                    <td>
                        @if($usuario->rol)<span class="badge" style="background:#e8eaf6;color:var(--navy);">{{ ucfirst(str_replace('_',' ',$usuario->rol->nombre)) }}</span>
                        @else <span class="badge bg-light text-dark border">Sin rol</span>@endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-action btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></a>
                            @if(auth()->user()->puede('usuarios','editar'))
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-action btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('permisos.index', $usuario) }}" class="btn btn-action btn-outline-info" title="Permisos"><i class="bi bi-shield-lock"></i></a>
                            @endif
                            @if(auth()->user()->puede('usuarios','eliminar'))
                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline" data-nombre="{{ $usuario->name }}" onsubmit="return confirmarEliminar(event, this.dataset.nombre)">
                                @csrf @method('DELETE')
                                <button class="btn btn-action btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-person-x"></i><p>Sin empleados con acceso.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tarjetas --}}
    <div class="view-tarjetas grid-cards">
        @foreach($conAcceso as $usuario)
        <div class="list-card sort-card"
             data-nombre="{{ strtolower($usuario->name) }}"
             data-cargo="{{ strtolower($usuario->cargo ?? '') }}"
             data-rol="{{ strtolower($usuario->rol->nombre ?? '') }}">
            <div class="d-flex align-items-center gap-3">
                <span style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($usuario->name,0,1)) }}</span>
                <div style="min-width:0">
                    <div class="card-title">{{ $usuario->name }}</div>
                    <div class="card-meta">{{ $usuario->email }}</div>
                </div>
            </div>
            <div class="card-meta">
                <i class="bi bi-briefcase"></i> {{ $usuario->cargo ?? 'Sin cargo' }}
                @if($usuario->rol)
                <span class="ms-auto badge" style="background:#e8eaf6;color:var(--navy);font-size:.65rem;">{{ ucfirst(str_replace('_',' ',$usuario->rol->nombre)) }}</span>
                @endif
            </div>
            @if($usuario->horario)
            <div class="card-meta"><i class="bi bi-clock"></i> {{ $usuario->horario }}</div>
            @endif
            <div class="card-actions">
                <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-eye me-1"></i>Ver</a>
                @if(auth()->user()->puede('usuarios','editar'))
                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                <a href="{{ route('permisos.index', $usuario) }}" class="btn btn-sm btn-outline-info" title="Permisos"><i class="bi bi-shield-lock"></i></a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Sin acceso ── --}}
<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon" style="background:#9e9e9e;color:#fff;width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.9rem;"><i class="bi bi-person"></i></div>
        Sin acceso al sistema
        <span class="badge ms-2 bg-secondary">{{ $sinAcceso->count() }}</span>
    </div>

    <div class="list-toolbar">
        <div class="d-flex gap-1">
            <button class="btn-vista active" data-v="tabla" title="Vista tabla"><i class="bi bi-table"></i></button>
            <button class="btn-vista" data-v="tarjetas" title="Vista tarjetas"><i class="bi bi-grid-3x3-gap"></i></button>
        </div>
        <div class="sep"></div>
        <span class="small text-muted">Ordenar:</span>
        <select class="form-select sort-select">
            <option value="nombre">Nombre</option>
            <option value="cargo">Cargo</option>
        </select>
        <button class="btn-sortdir" title="Cambiar dirección"><i class="bi bi-sort-down"></i></button>
    </div>

    {{-- Tabla --}}
    <div class="view-tabla table-responsive">
        <table class="table">
            <thead>
                <tr><th>Empleado</th><th>Cargo</th><th>Teléfono</th><th>Horario</th><th>Días laborales</th><th class="text-center">Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($sinAcceso as $usuario)
                <tr class="sort-row"
                    data-nombre="{{ strtolower($usuario->name) }}"
                    data-cargo="{{ strtolower($usuario->cargo ?? '') }}">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:34px;height:34px;border-radius:50%;background:#e0e0e0;color:#616161;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($usuario->name,0,1)) }}</span>
                            <div class="fw-semibold" style="font-size:.9rem;">{{ $usuario->name }}</div>
                        </div>
                    </td>
                    <td class="small text-muted">{{ $usuario->cargo ?? '—' }}</td>
                    <td class="small">{{ $usuario->telefono ?? '—' }}</td>
                    <td class="small">@if($usuario->horario)<i class="bi bi-clock me-1 text-muted"></i>{{ $usuario->horario }}@else —@endif</td>
                    <td>
                        @if($usuario->dias_laborales)
                            @foreach(explode(',',$usuario->dias_laborales) as $dia)<span class="day-chip">{{ trim($dia) }}</span>@endforeach
                        @else <span class="text-muted small">—</span>@endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-action btn-outline-primary"><i class="bi bi-eye"></i></a>
                            @if(auth()->user()->puede('usuarios','editar'))
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-action btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            @endif
                            @if(auth()->user()->puede('usuarios','eliminar'))
                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline" data-nombre="{{ $usuario->name }}" onsubmit="return confirmarEliminar(event, this.dataset.nombre)">
                                @csrf @method('DELETE')
                                <button class="btn btn-action btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-person"></i><p>Sin empleados sin acceso.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tarjetas --}}
    <div class="view-tarjetas grid-cards">
        @foreach($sinAcceso as $usuario)
        <div class="list-card sort-card"
             data-nombre="{{ strtolower($usuario->name) }}"
             data-cargo="{{ strtolower($usuario->cargo ?? '') }}">
            <div class="d-flex align-items-center gap-3">
                <span style="width:42px;height:42px;border-radius:50%;background:#e0e0e0;color:#616161;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($usuario->name,0,1)) }}</span>
                <div>
                    <div class="card-title">{{ $usuario->name }}</div>
                    <div class="card-meta">{{ $usuario->cargo ?? 'Sin cargo' }}</div>
                </div>
            </div>
            @if($usuario->telefono)
            <div class="card-meta"><i class="bi bi-telephone"></i> {{ $usuario->telefono }}</div>
            @endif
            @if($usuario->horario)
            <div class="card-meta"><i class="bi bi-clock"></i> {{ $usuario->horario }}</div>
            @endif
            <div class="card-actions">
                <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-eye me-1"></i>Ver</a>
                @if(auth()->user()->puede('usuarios','editar'))
                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection

@section('scripts')
<script>
function confirmarEliminar(e, nombre) {
    if (!confirm(`¿Eliminar al empleado "${nombre}"?\n\nEsta acción no se puede deshacer.`)) { e.preventDefault(); return false; }
    return true;
}
initListView('usuarios_acceso', 'nombre', 'asc');
initListView('usuarios_sin', 'nombre', 'asc');
</script>
@endsection
