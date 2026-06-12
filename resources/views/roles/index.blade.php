@extends('layouts.app')
@section('title', 'Gestión de Roles y Permisos')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon navy"><i class="bi bi-shield-shaded"></i></div>
        <div>
            <h2>Roles y Permisos</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Roles y Permisos</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="small text-muted d-none d-md-block" style="max-width:340px;text-align:right;line-height:1.4;">
        <i class="bi bi-info-circle me-1"></i>
        Los permisos del rol aplican a todos sus usuarios.<br>
        Los permisos individuales se gestionan en el perfil de cada empleado.
    </div>
</div>

@if(session('success'))
<div class="alert alert-success mb-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger mb-3">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

{{-- Tabs de roles --}}
<ul class="nav nav-pills mb-3 flex-wrap gap-1" id="rolesTabs">
    @foreach($roles as $i => $rol)
    @if($rol->nombre !== 'super_admin')
    <li class="nav-item">
        <button class="nav-link {{ $i === 1 ? 'active' : '' }}" data-rol="{{ $rol->id }}">
            <i class="bi bi-person-badge me-1"></i>
            {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
        </button>
    </li>
    @endif
    @endforeach
</ul>

{{-- Paneles por rol --}}
@foreach($roles as $i => $rol)
@if($rol->nombre !== 'super_admin')
<div class="rol-panel {{ $i === 1 ? '' : 'd-none' }}" data-rol="{{ $rol->id }}">
    <div class="data-card">
        <div class="data-card-header">
            <div class="header-icon navy"><i class="bi bi-shield-lock"></i></div>
            {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
            <span class="text-muted ms-2" style="font-size:.78rem;font-weight:400;">— {{ $rol->descripcion }}</span>
        </div>

        <form action="{{ route('roles.update', $rol) }}" method="POST">
            @csrf @method('PUT')

            <div class="table-responsive">
                <table class="table perm-table mb-0">
                    <thead>
                        <tr>
                            <th style="min-width:220px;">Módulo</th>
                            <th class="text-center" style="width:90px;"><i class="bi bi-eye me-1"></i>Ver</th>
                            <th class="text-center" style="width:90px;"><i class="bi bi-plus-circle me-1"></i>Crear</th>
                            <th class="text-center" style="width:90px;"><i class="bi bi-pencil me-1"></i>Editar</th>
                            <th class="text-center" style="width:90px;"><i class="bi bi-trash me-1"></i>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($moduloNombres as $moduloKey => [$moduloLabel, $moduloIcon])
                        @php $accionesModulo = $permisos->get($moduloKey) ?? collect(); @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width:28px;height:28px;border-radius:7px;background:#e8eaf6;color:var(--navy);display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;">
                                        <i class="bi {{ $moduloIcon }}"></i>
                                    </span>
                                    <span class="fw-semibold" style="font-size:.88rem;">{{ $moduloLabel }}</span>
                                </div>
                            </td>
                            @foreach(['ver','crear','editar','eliminar'] as $accion)
                            @php
                                $permiso   = $accionesModulo->firstWhere('accion', $accion);
                                $tienePermiso = $permiso && $rol->permisos->contains('id', $permiso->id);
                            @endphp
                            <td class="text-center">
                                @if($permiso)
                                <div class="form-check d-flex justify-content-center">
                                    <input type="checkbox"
                                           class="form-check-input perm-check"
                                           name="permisos[]"
                                           value="{{ $permiso->id }}"
                                           {{ $tienePermiso ? 'checked' : '' }}>
                                </div>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 d-flex gap-2 align-items-center border-top" style="border-color:var(--border-color)!important;">
                <button type="submit" class="btn btn-navy">
                    <i class="bi bi-save me-1"></i>Guardar permisos
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="marcarTodos(this)">
                    <i class="bi bi-check-all me-1"></i>Marcar todos
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="desmarcarTodos(this)">
                    <i class="bi bi-x-lg me-1"></i>Desmarcar todos
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach

{{-- Info super_admin --}}
<div class="data-card mt-3 p-3" style="background:var(--bg-card-alt);border:1px solid var(--border-color);">
    <div class="d-flex align-items-center gap-2 text-muted small">
        <i class="bi bi-shield-fill-check fs-5" style="color:var(--navy);"></i>
        <span><strong>Super Admin</strong> — tiene acceso total al sistema de forma automática. Sus permisos no son editables.</span>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Tabs
document.querySelectorAll('#rolesTabs .nav-link').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('#rolesTabs .nav-link').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const rolId = this.dataset.rol;
        document.querySelectorAll('.rol-panel').forEach(p => {
            p.classList.toggle('d-none', p.dataset.rol !== rolId);
        });
    });
});

// Marcar/desmarcar todos dentro del panel activo
function marcarTodos(btn) {
    btn.closest('.rol-panel').querySelectorAll('.perm-check').forEach(cb => cb.checked = true);
}
function desmarcarTodos(btn) {
    btn.closest('.rol-panel').querySelectorAll('.perm-check').forEach(cb => cb.checked = false);
}
</script>
@endsection
