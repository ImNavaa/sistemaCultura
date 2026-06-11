@extends('layouts.app')
@section('title', 'Días Pendientes')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon teal"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <h2>Días Pendientes</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('rh.dashboard') }}">RH</a></li>
                    <li class="breadcrumb-item active">Días Pendientes</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->puede('usuarios','editar'))
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevo">
            <i class="bi bi-plus-lg me-1"></i>Registrar día pendiente
        </button>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2 mb-3">
    {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Resumen por empleado (solo los que tienen pendientes) --}}
@if($pendientesPorEmpleado->isNotEmpty())
<div class="data-card mb-3">
    <div class="data-card-header">
        <div class="header-icon amber"><i class="bi bi-exclamation-triangle"></i></div>
        Días pendientes sin usar
    </div>
    <div class="p-3 d-flex flex-wrap gap-2">
        @foreach($pendientesPorEmpleado as $uid => $count)
        @php $emp = $empleados->find($uid); @endphp
        @if($emp)
        <a href="{{ request()->fullUrlWithQuery(['empleado' => $uid]) }}"
           class="d-flex align-items-center gap-2 text-decoration-none px-3 py-2 rounded-3"
           style="background:{{ $filtroUser == $uid ? 'var(--accent,#3a7bd5)' : 'var(--bg-card-alt)' }};
                  color:{{ $filtroUser == $uid ? '#fff' : 'var(--text-main)' }};
                  border:1px solid var(--border-color);font-size:.82rem;">
            <span class="fw-semibold">{{ $emp->name }}</span>
            <span class="badge" style="background:{{ $filtroUser == $uid ? 'rgba(255,255,255,.25)' : '#fee2e2' }};color:{{ $filtroUser == $uid ? '#fff' : '#b91c1c' }};">{{ $count }}</span>
        </a>
        @endif
        @endforeach
        @if($filtroUser)
        <a href="{{ route('dias-pendientes.index') }}"
           class="d-flex align-items-center gap-1 text-decoration-none px-3 py-2 rounded-3 text-muted"
           style="background:var(--bg-card-alt);border:1px solid var(--border-color);font-size:.82rem;">
            <i class="bi bi-x-lg"></i> Ver todos
        </a>
        @endif
    </div>
</div>
@endif

{{-- Tabla de registros --}}
<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon teal"><i class="bi bi-list-check"></i></div>
        Historial de días pendientes
        <span class="badge ms-auto" style="background:#e0f2f1;color:#00695c;">{{ $registros->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Fecha generación</th>
                    <th>Motivo</th>
                    <th class="text-center">Estado</th>
                    <th>Fecha uso</th>
                    <th>Registrado por</th>
                    @if(auth()->user()->puede('usuarios','editar'))
                    <th class="text-center">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $d)
                <tr>
                    <td>
                        <a href="{{ route('usuarios.show', $d->empleado) }}" class="text-decoration-none fw-semibold" style="font-size:.88rem;">
                            {{ $d->empleado->name }}
                        </a>
                    </td>
                    <td class="small">{{ $d->fecha_generacion->format('d/m/Y') }}</td>
                    <td style="font-size:.88rem;">{{ $d->motivo }}</td>
                    <td class="text-center">
                        @if($d->isPendiente())
                        <span class="badge" style="background:#fee2e2;color:#b91c1c;">Pendiente</span>
                        @else
                        <span class="badge" style="background:#dcfce7;color:#15803d;">Utilizado</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $d->fecha_uso?->format('d/m/Y') ?? '—' }}</td>
                    <td class="small text-muted">{{ $d->registrador?->name ?? '—' }}</td>
                    @if(auth()->user()->puede('usuarios','editar'))
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            @if($d->isPendiente())
                            <button class="btn btn-action btn-outline-success" title="Marcar como utilizado"
                                    onclick="abrirUsar({{ $d->id }}, '{{ addslashes($d->empleado->name) }}')">
                                <i class="bi bi-check2"></i>
                            </button>
                            @endif
                            <form action="{{ route('dias-pendientes.destroy', $d) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este registro?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-action btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-calendar-check"></i>
                            <p>No hay días pendientes registrados.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal nuevo --}}
<div class="modal fade" id="modalNuevo" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h6 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Registrar Día Pendiente</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dias-pendientes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Empleado <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select form-select-sm" required>
                            <option value="">Seleccionar…</option>
                            @foreach($empleados as $emp)
                            <option value="{{ $emp->id }}" {{ $filtroUser == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fecha en que se generó <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_generacion" class="form-control form-control-sm"
                               value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold">Motivo <span class="text-danger">*</span></label>
                        <input type="text" name="motivo" class="form-control form-control-sm"
                               placeholder="Ej: Trabajó domingo 8 de junio" required>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save me-1"></i>Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal marcar utilizado --}}
<div class="modal fade" id="modalUsar" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h6 class="modal-title"><i class="bi bi-check2-circle me-2"></i>Marcar como utilizado</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsar" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <p class="small mb-3">Empleado: <strong id="usarNombre">—</strong></p>
                    <div>
                        <label class="form-label small fw-semibold">Fecha en que se utilizó</label>
                        <input type="date" name="fecha_uso" class="form-control form-control-sm"
                               value="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check2 me-1"></i>Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
function abrirUsar(id, nombre) {
    document.getElementById('usarNombre').textContent = nombre;
    document.getElementById('formUsar').action = '/dias-pendientes/' + id + '/usar';
    new bootstrap.Modal(document.getElementById('modalUsar')).show();
}
</script>
@endsection
