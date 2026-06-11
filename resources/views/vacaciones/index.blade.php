@extends('layouts.app')
@section('title', 'Vacaciones')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon indigo"><i class="bi bi-umbrella"></i></div>
        <div>
            <h2>Control de Vacaciones</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('rh.dashboard') }}">RH</a></li>
                    <li class="breadcrumb-item active">Vacaciones</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        {{-- Selector de año --}}
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="small fw-semibold mb-0">Año:</label>
            <select name="anio" class="form-select form-select-sm" style="width:90px;" onchange="this.form.submit()">
                @foreach($anios as $a)
                <option value="{{ $a }}" {{ $a == $anio ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </form>
        @if(auth()->user()->puede('usuarios','editar'))
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAsignar">
            <i class="bi bi-plus-lg me-1"></i>Asignar vacaciones
        </button>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2 mb-3">
    {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show py-2 mb-3">
    {{ $errors->first() }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon indigo"><i class="bi bi-table"></i></div>
        Personal — Vacaciones {{ $anio }}
        <span class="badge ms-auto" style="background:#e0e7ff;color:#3730a3;">{{ $empleados->count() }} empleados</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Recinto</th>
                    <th class="text-center">Asignados</th>
                    <th class="text-center">Usados</th>
                    <th class="text-center">Disponibles</th>
                    <th class="text-center">Progreso</th>
                    @if(auth()->user()->puede('usuarios','editar'))
                    <th class="text-center">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($empleados as $emp)
                @php
                    $vac  = $emp->vacaciones->first();
                    $asig = $vac?->dias_asignados ?? 0;
                    $usad = $vac?->dias_usados ?? 0;
                    $disp = $vac ? $vac->diasDisponibles() : 0;
                    $pct  = $asig > 0 ? intval($usad / $asig * 100) : 0;
                    $color = $disp === 0 ? '#dc2626' : ($disp <= 3 ? '#f59e0b' : '#16a34a');
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('usuarios.show', $emp) }}" class="text-decoration-none fw-semibold" style="font-size:.9rem;">
                            {{ $emp->name }}
                        </a>
                        @if($emp->cargo)
                        <div class="small text-muted">{{ $emp->cargo }}</div>
                        @endif
                    </td>
                    <td>
                        @if($emp->recinto)
                        <span class="badge" style="background:#e8eaf6;color:#283593;font-size:.75rem;">{{ $emp->recinto }}</span>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-center fw-semibold">{{ $asig ?: '—' }}</td>
                    <td class="text-center">{{ $usad ?: '0' }}</td>
                    <td class="text-center fw-bold" style="color:{{ $color }}">{{ $asig ? $disp : '—' }}</td>
                    <td style="min-width:120px;">
                        @if($asig > 0)
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-fill" style="height:8px;border-radius:4px;">
                                <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};border-radius:4px;"></div>
                            </div>
                            <span class="small text-muted" style="font-size:.72rem;white-space:nowrap;">{{ $pct }}%</span>
                        </div>
                        @else
                        <span class="small text-muted">Sin asignar</span>
                        @endif
                    </td>
                    @if(auth()->user()->puede('usuarios','editar'))
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-action btn-outline-primary" title="Asignar/editar"
                                    onclick="abrirAsignar({{ $emp->id }}, '{{ addslashes($emp->name) }}', {{ $asig }}, {{ $anio }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($vac && $disp > 0)
                            <button class="btn btn-action btn-outline-success" title="Registrar uso"
                                    onclick="abrirUsar({{ $vac->id }}, '{{ addslashes($emp->name) }}', {{ $disp }})">
                                <i class="bi bi-check2-square"></i>
                            </button>
                            @endif
                            @if($vac)
                            <form action="{{ route('vacaciones.destroy', $vac) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar registro de vacaciones de {{ $emp->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-action btn-outline-danger" title="Eliminar registro"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Asignar vacaciones --}}
<div class="modal fade" id="modalAsignar" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h6 class="modal-title"><i class="bi bi-umbrella me-2"></i>Asignar Vacaciones</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('vacaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Empleado</label>
                        <select name="user_id" class="form-select form-select-sm" id="selectEmpleado" required>
                            <option value="">Seleccionar…</option>
                            @foreach($empleados as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Año</label>
                            <input type="number" name="anio" class="form-control form-control-sm" id="inputAnio"
                                   value="{{ $anio }}" min="2020" max="2100" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Días asignados</label>
                            <input type="number" name="dias_asignados" class="form-control form-control-sm"
                                   id="inputDiasAsig" value="15" min="0" max="365" required>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label small fw-semibold">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save me-1"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Registrar uso --}}
<div class="modal fade" id="modalUsar" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h6 class="modal-title"><i class="bi bi-check2-square me-2"></i>Registrar uso de vacaciones</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsar" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <p class="small mb-3">Empleado: <strong id="usarNombre">—</strong></p>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Días a usar</label>
                        <input type="number" name="dias" class="form-control form-control-sm" min="1" required>
                        <div class="form-text small">Disponibles: <strong id="usarDisp">—</strong></div>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold">Observación (opcional)</label>
                        <input type="text" name="observaciones" class="form-control form-control-sm" placeholder="Período, motivo…">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check2 me-1"></i>Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
function abrirAsignar(userId, nombre, diasActuales, anio) {
    document.getElementById('selectEmpleado').value = userId;
    document.getElementById('inputAnio').value      = anio;
    document.getElementById('inputDiasAsig').value  = diasActuales || 15;
    var modal = new bootstrap.Modal(document.getElementById('modalAsignar'));
    modal.show();
}
function abrirUsar(vacId, nombre, disp) {
    document.getElementById('usarNombre').textContent = nombre;
    document.getElementById('usarDisp').textContent   = disp;
    document.getElementById('formUsar').action = '/vacaciones/' + vacId + '/usar';
    document.querySelector('#formUsar input[name="dias"]').max = disp;
    document.querySelector('#formUsar input[name="dias"]').value = '';
    var modal = new bootstrap.Modal(document.getElementById('modalUsar'));
    modal.show();
}
</script>
@endsection
