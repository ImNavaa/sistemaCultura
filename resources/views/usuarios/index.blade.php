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

{{-- ── Buscador global ── --}}
<div class="mb-3">
    <div class="input-group" style="max-width:420px;">
        <span class="input-group-text" style="background:var(--bg-card);border-color:var(--border-color);">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="search" id="buscarPersonal" class="form-control"
               placeholder="Buscar por nombre, cargo o rol…"
               style="background:var(--bg-card);border-color:var(--border-color);color:var(--text-main);"
               autocomplete="off">
    </div>
</div>

{{-- ── Con acceso ── --}}
<div class="data-card mb-4">
    <div class="data-card-header">
        <div class="header-icon blue"><i class="bi bi-person-check"></i></div>
        Con acceso al sistema
        <span class="badge ms-2" style="background:#e3f2fd;color:#1565c0;">{{ $conAcceso->count() }}</span>
        <span class="ms-auto small text-muted d-none d-md-inline" style="font-weight:400;font-size:.73rem;">
            <i class="bi bi-hand-index me-1"></i>Selecciona una fila para ver opciones
        </span>
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
        <table class="table tabla-clickable">
            <thead>
                <tr>
                    <th>Empleado</th><th>Cargo</th><th>Horario</th><th>Días laborales</th><th>Rol</th><th style="width:28px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($conAcceso as $usuario)
                @php
                    $diasStr = $usuario->dias_laborales ? implode(', ', array_map('trim', explode(',', $usuario->dias_laborales))) : null;
                @endphp
                <tr class="fila-clickable sort-row"
                    data-nombre="{{ strtolower($usuario->name) }}"
                    data-cargo="{{ strtolower($usuario->cargo ?? '') }}"
                    data-rol="{{ strtolower($usuario->rol->nombre ?? '') }}"
                    data-json='@json([
                        "id"           => $usuario->id,
                        "nombre"       => $usuario->name,
                        "inicial"      => strtoupper(substr($usuario->name, 0, 1)),
                        "email"        => $usuario->email,
                        "cargo"        => $usuario->cargo,
                        "rol"          => $usuario->rol?->nombre,
                        "horario"      => $usuario->horario,
                        "dias"         => $diasStr,
                        "telefono"     => $usuario->telefono ?? null,
                        "con_acceso"   => true,
                        "show_url"     => route("usuarios.show", $usuario),
                        "edit_url"     => auth()->user()->puede("usuarios","editar") ? route("usuarios.edit", $usuario) : null,
                        "permisos_url" => auth()->user()->puede("usuarios","editar") ? route("permisos.index", $usuario) : null,
                        "destroy_url"  => auth()->user()->puede("usuarios","eliminar") ? route("usuarios.destroy", $usuario) : null,
                    ])'>
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
                    <td><i class="bi bi-chevron-right text-muted" style="font-size:.75rem;"></i></td>
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
        @php
            $diasStr2 = $usuario->dias_laborales ? implode(', ', array_map('trim', explode(',', $usuario->dias_laborales))) : null;
        @endphp
        <div class="list-card sort-card fila-clickable"
             data-nombre="{{ strtolower($usuario->name) }}"
             data-cargo="{{ strtolower($usuario->cargo ?? '') }}"
             data-rol="{{ strtolower($usuario->rol->nombre ?? '') }}"
             data-json='@json([
                 "id"           => $usuario->id,
                 "nombre"       => $usuario->name,
                 "inicial"      => strtoupper(substr($usuario->name, 0, 1)),
                 "email"        => $usuario->email,
                 "cargo"        => $usuario->cargo,
                 "rol"          => $usuario->rol?->nombre,
                 "horario"      => $usuario->horario,
                 "dias"         => $diasStr2,
                 "telefono"     => $usuario->telefono ?? null,
                 "con_acceso"   => true,
                 "show_url"     => route("usuarios.show", $usuario),
                 "edit_url"     => auth()->user()->puede("usuarios","editar") ? route("usuarios.edit", $usuario) : null,
                 "permisos_url" => auth()->user()->puede("usuarios","editar") ? route("permisos.index", $usuario) : null,
                 "destroy_url"  => auth()->user()->puede("usuarios","eliminar") ? route("usuarios.destroy", $usuario) : null,
             ])'>
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
                <span class="text-muted small flex-fill" style="font-size:.72rem;"><i class="bi bi-hand-index me-1"></i>Clic para ver opciones</span>
                <i class="bi bi-chevron-right text-muted"></i>
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
        <table class="table tabla-clickable">
            <thead>
                <tr><th>Empleado</th><th>Cargo</th><th>Teléfono</th><th>Horario</th><th>Días laborales</th><th style="width:28px;"></th></tr>
            </thead>
            <tbody>
                @forelse($sinAcceso as $usuario)
                @php
                    $diasStr3 = $usuario->dias_laborales ? implode(', ', array_map('trim', explode(',', $usuario->dias_laborales))) : null;
                @endphp
                <tr class="fila-clickable sort-row"
                    data-nombre="{{ strtolower($usuario->name) }}"
                    data-cargo="{{ strtolower($usuario->cargo ?? '') }}"
                    data-json='@json([
                        "id"          => $usuario->id,
                        "nombre"      => $usuario->name,
                        "inicial"     => strtoupper(substr($usuario->name, 0, 1)),
                        "email"       => null,
                        "cargo"       => $usuario->cargo,
                        "rol"         => null,
                        "horario"     => $usuario->horario,
                        "dias"        => $diasStr3,
                        "telefono"    => $usuario->telefono ?? null,
                        "con_acceso"  => false,
                        "show_url"    => route("usuarios.show", $usuario),
                        "edit_url"    => auth()->user()->puede("usuarios","editar") ? route("usuarios.edit", $usuario) : null,
                        "permisos_url"=> null,
                        "destroy_url" => auth()->user()->puede("usuarios","eliminar") ? route("usuarios.destroy", $usuario) : null,
                    ])'>
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
                    <td><i class="bi bi-chevron-right text-muted" style="font-size:.75rem;"></i></td>
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
        @php
            $diasStr4 = $usuario->dias_laborales ? implode(', ', array_map('trim', explode(',', $usuario->dias_laborales))) : null;
        @endphp
        <div class="list-card sort-card fila-clickable"
             data-nombre="{{ strtolower($usuario->name) }}"
             data-cargo="{{ strtolower($usuario->cargo ?? '') }}"
             data-json='@json([
                 "id"          => $usuario->id,
                 "nombre"      => $usuario->name,
                 "inicial"     => strtoupper(substr($usuario->name, 0, 1)),
                 "email"       => null,
                 "cargo"       => $usuario->cargo,
                 "rol"         => null,
                 "horario"     => $usuario->horario,
                 "dias"        => $diasStr4,
                 "telefono"    => $usuario->telefono ?? null,
                 "con_acceso"  => false,
                 "show_url"    => route("usuarios.show", $usuario),
                 "edit_url"    => auth()->user()->puede("usuarios","editar") ? route("usuarios.edit", $usuario) : null,
                 "permisos_url"=> null,
                 "destroy_url" => auth()->user()->puede("usuarios","eliminar") ? route("usuarios.destroy", $usuario) : null,
             ])'>
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
                <span class="text-muted small flex-fill" style="font-size:.72rem;"><i class="bi bi-hand-index me-1"></i>Clic para ver opciones</span>
                <i class="bi bi-chevron-right text-muted"></i>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Form oculto para eliminar --}}
<form id="formEliminarUser" method="POST" class="d-none">
    @csrf @method('DELETE')
</form>

{{-- ══ PANEL LATERAL ═══════════════════════════════════════ --}}
<div class="offcanvas offcanvas-end d-flex flex-column" tabindex="-1" id="panelDetalle" style="width:370px;max-width:95vw;">
    <div class="offcanvas-header pb-3" id="panelHeader" style="background:linear-gradient(135deg,var(--navy),var(--navy3));">
        <div class="d-flex align-items-center gap-3">
            <span id="panelAvatar" style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.18);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.2rem;flex-shrink:0;"></span>
            <div>
                <div class="text-white fw-bold lh-sm" id="panelNombre" style="font-size:1rem;"></div>
                <div id="panelSubtitulo" style="color:rgba(255,255,255,.7);font-size:.78rem;"></div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body flex-grow-1 overflow-auto p-3" id="panelCuerpo"></div>
    <div class="p-3 border-top d-flex flex-column gap-2" id="panelFooter" style="background:var(--bg-card-alt);">
        <a id="panelBtnVer" href="#" class="btn btn-navy">
            <i class="bi bi-eye me-1"></i>Ver perfil completo
        </a>
        <div class="d-flex gap-2">
            <a id="panelBtnEditar" href="#" class="btn btn-outline-warning flex-fill d-none">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
            <a id="panelBtnPermisos" href="#" class="btn btn-outline-info flex-fill d-none">
                <i class="bi bi-shield-lock me-1"></i>Permisos
            </a>
        </div>
        <button id="panelBtnEliminar" class="btn btn-outline-danger d-none" onclick="eliminarPanel()">
            <i class="bi bi-trash me-1"></i>Eliminar empleado
        </button>
    </div>
</div>

@endsection

@section('scripts')
<style>
.fila-clickable { cursor: pointer; user-select: none; }
.view-tabla .fila-clickable:hover td { background: var(--bg-row-hover) !important; }
.view-tabla .fila-seleccionada td { background: #e8eaf620 !important; }
[data-theme="dark"] .view-tabla .fila-seleccionada td { background: #1a237e15 !important; }
.list-card.fila-clickable { transition: box-shadow .15s, transform .1s; }
.list-card.fila-clickable:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); transform: translateY(-1px); }
.list-card.fila-seleccionada { border-color: var(--navy3) !important; box-shadow: 0 0 0 2px var(--navy3)33 !important; }
.panel-campo { display:flex; gap:.65rem; align-items:flex-start; padding:.55rem 0; border-bottom:1px solid var(--border-color); }
.panel-campo:last-child { border-bottom: none; }
.panel-campo-icon { color:var(--text-muted); font-size:.85rem; width:16px; flex-shrink:0; margin-top:2px; }
.panel-campo-label { font-size:.68rem; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); }
.panel-campo-val { font-size:.875rem; color:var(--text-main); }
</style>
<script>
let _panelData = {};

function campo(icon, label, val) {
    if (!val && val !== 0) return '';
    return `<div class="panel-campo">
        <i class="bi ${icon} panel-campo-icon"></i>
        <div><div class="panel-campo-label">${label}</div><div class="panel-campo-val">${val}</div></div>
    </div>`;
}

function abrirPanel(el, d) {
    _panelData = d;

    document.querySelectorAll('.fila-clickable').forEach(f => f.classList.remove('fila-seleccionada'));
    el.classList.add('fila-seleccionada');

    const gradAcc  = 'linear-gradient(135deg,var(--navy),var(--navy3))';
    const gradNoAcc= 'linear-gradient(135deg,#616161,#9e9e9e)';
    document.getElementById('panelHeader').style.background = d.con_acceso ? gradAcc : gradNoAcc;
    document.getElementById('panelAvatar').textContent      = d.inicial || d.nombre.charAt(0).toUpperCase();
    document.getElementById('panelNombre').textContent      = d.nombre;
    document.getElementById('panelSubtitulo').textContent   = d.con_acceso ? 'Con acceso al sistema' : 'Sin acceso al sistema';
    document.getElementById('panelBtnVer').href             = d.show_url;

    const btnEd = document.getElementById('panelBtnEditar');
    const btnPe = document.getElementById('panelBtnPermisos');
    const btnEl = document.getElementById('panelBtnEliminar');

    d.edit_url    ? (btnEd.href = d.edit_url, btnEd.classList.remove('d-none')) : btnEd.classList.add('d-none');
    d.permisos_url? (btnPe.href = d.permisos_url, btnPe.classList.remove('d-none')): btnPe.classList.add('d-none');
    d.destroy_url ? btnEl.classList.remove('d-none') : btnEl.classList.add('d-none');

    let rolHtml = d.rol
        ? `<span class="badge" style="background:#e8eaf6;color:var(--navy);">${d.rol.replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase())}</span>`
        : '<span class="badge bg-light text-dark border">Sin rol</span>';

    let html = '';
    if (d.email)   html += campo('bi-envelope',   'Email',          d.email);
    if (d.telefono)html += campo('bi-telephone',  'Teléfono',       d.telefono);
    if (d.cargo)   html += campo('bi-briefcase',  'Cargo',          d.cargo);
    if (d.con_acceso) html += campo('bi-shield',  'Rol del sistema', rolHtml);
    if (d.horario) html += campo('bi-clock',      'Horario',        d.horario);
    if (d.dias)    html += campo('bi-calendar-week','Días laborales', d.dias);

    document.getElementById('panelCuerpo').innerHTML = html || '<p class="text-muted small mt-2">Sin información adicional.</p>';

    bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('panelDetalle')).show();
}

document.querySelectorAll('.fila-clickable').forEach(function (el) {
    el.addEventListener('click', function () {
        abrirPanel(this, JSON.parse(this.dataset.json));
    });
});

function eliminarPanel() {
    if (!_panelData.destroy_url) return;
    if (!confirm(`¿Eliminar al empleado "${_panelData.nombre}"?\n\nEsta acción no se puede deshacer.`)) return;
    const form = document.getElementById('formEliminarUser');
    form.action = _panelData.destroy_url;
    form.submit();
}

function confirmarEliminar(e, nombre) {
    if (!confirm(`¿Eliminar al empleado "${nombre}"?\n\nEsta acción no se puede deshacer.`)) { e.preventDefault(); return false; }
    return true;
}

initListView('usuarios_acceso', 'nombre', 'asc');
initListView('usuarios_sin', 'nombre', 'asc');

document.getElementById('buscarPersonal').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.sort-row, .sort-card').forEach(el => {
        const nombre = (el.dataset.nombre || '');
        const cargo  = (el.dataset.cargo  || '');
        const rol    = (el.dataset.rol    || '');
        const match  = !q || nombre.includes(q) || cargo.includes(q) || rol.includes(q);
        el.style.display = match ? '' : 'none';
    });
});
</script>
@endsection
