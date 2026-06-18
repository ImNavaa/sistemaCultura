@extends('layouts.app')
@section('title', 'Directorio de Asistentes')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon teal"><i class="bi bi-people"></i></div>
        <div>
            <h2>Directorio de Asistentes</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('actividades.index') }}">Actividades</a></li>
                    <li class="breadcrumb-item active">Directorio</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<form method="GET" class="mb-3 d-flex gap-2 align-items-center">
    <div class="input-group" style="max-width:360px;">
        <span class="input-group-text" style="background:var(--bg-card);border-color:var(--border-color);"><i class="bi bi-search text-muted"></i></span>
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, email o institución…"
               value="{{ $q }}" style="background:var(--bg-card);border-color:var(--border-color);color:var(--text-main);">
    </div>
    <button type="submit" class="btn btn-navy btn-sm"><i class="bi bi-funnel me-1"></i>Buscar</button>
    @if($q)<a href="{{ route('asistentes.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x me-1"></i>Limpiar</a>@endif
</form>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon teal"><i class="bi bi-people"></i></div>
        Personas registradas
        <span class="badge ms-2 bg-secondary">{{ $asistentes->total() }}</span>
        <span class="ms-auto small text-muted d-none d-md-inline" style="font-weight:400;font-size:.73rem;">
            <i class="bi bi-hand-index me-1"></i>Selecciona una fila para ver opciones
        </span>
    </div>
    <div class="table-responsive">
        <table class="table tabla-clickable">
            <thead>
                <tr>
                    <th>Persona</th>
                    <th>Contacto</th>
                    <th>Institución</th>
                    <th>Ciudad</th>
                    <th class="text-center">Eventos</th>
                    <th style="width:28px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($asistentes as $ast)
                @php
                    $astData = [
                        'nombre'         => $ast->nombreCompleto(),
                        'iniciales'      => $ast->iniciales(),
                        'email'          => $ast->email,
                        'telefono'       => $ast->telefono,
                        'institucion'    => $ast->institucion,
                        'ciudad'         => $ast->ciudad,
                        'ocupacion'      => $ast->ocupacion,
                        'redes_sociales' => $ast->redes_sociales,
                        'eventos'        => $ast->inscripciones_count,
                        'show_url'       => route('asistentes.show', $ast),
                    ];
                @endphp
                <tr class="fila-clickable" data-json='@json($astData)'>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#00695c,#00897b);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex-shrink:0;">{{ $ast->iniciales() }}</span>
                            <div>
                                <div class="fw-semibold" style="color:var(--text-main);">{{ $ast->nombreCompleto() }}</div>
                                @if($ast->ocupacion)<div class="small text-muted">{{ $ast->ocupacion }}</div>@endif
                            </div>
                        </div>
                    </td>
                    <td class="small">
                        @if($ast->email)<div>{{ $ast->email }}</div>@endif
                        @if($ast->telefono)<div class="text-muted">{{ $ast->telefono }}</div>@endif
                        @if(! $ast->email && ! $ast->telefono)—@endif
                    </td>
                    <td class="small text-muted">{{ $ast->institucion ?? '—' }}</td>
                    <td class="small text-muted">{{ $ast->ciudad ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:#e3f2fd;color:#1565c0;">{{ $ast->inscripciones_count }}</span>
                    </td>
                    <td><i class="bi bi-chevron-right text-muted" style="font-size:.75rem;"></i></td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-people"></i><p>No se encontraron personas.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($asistentes->hasPages())
    <div class="p-3 d-flex justify-content-center">{{ $asistentes->links() }}</div>
    @endif
</div>

{{-- ══ PANEL LATERAL ═══════════════════════════════════════ --}}
<div class="offcanvas offcanvas-end d-flex flex-column" tabindex="-1" id="panelDetalle" style="width:370px;max-width:95vw;">
    <div class="offcanvas-header pb-3" style="background:linear-gradient(135deg,#00695c,#00897b);">
        <div class="d-flex align-items-center gap-3">
            <span id="panelAvatar" style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.18);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0;"></span>
            <div>
                <div class="text-white fw-bold lh-sm" id="panelNombre" style="font-size:1rem;"></div>
                <div style="color:rgba(255,255,255,.7);font-size:.78rem;">Asistente</div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body flex-grow-1 overflow-auto p-3" id="panelCuerpo"></div>
    <div class="p-3 border-top" id="panelFooter" style="background:var(--bg-card-alt);">
        <a id="panelBtnVer" href="#" class="btn btn-navy w-100">
            <i class="bi bi-person-lines-fill me-1"></i>Ver historial completo
        </a>
    </div>
</div>

@endsection

@section('scripts')
<style>
.fila-clickable { cursor: pointer; user-select: none; }
.fila-clickable:hover td { background: var(--bg-row-hover) !important; }
.fila-seleccionada td { background: #e0f2f120 !important; }
[data-theme="dark"] .fila-seleccionada td { background: #00695c18 !important; }
.panel-campo { display:flex; gap:.65rem; align-items:flex-start; padding:.55rem 0; border-bottom:1px solid var(--border-color); }
.panel-campo:last-child { border-bottom: none; }
.panel-campo-icon { color:var(--text-muted); font-size:.85rem; width:16px; flex-shrink:0; margin-top:2px; }
.panel-campo-label { font-size:.68rem; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); }
.panel-campo-val { font-size:.875rem; color:var(--text-main); }
</style>
<script>
function campo(icon, label, val) {
    if (!val && val !== 0) return '';
    return `<div class="panel-campo">
        <i class="bi ${icon} panel-campo-icon"></i>
        <div><div class="panel-campo-label">${label}</div><div class="panel-campo-val">${val}</div></div>
    </div>`;
}

document.querySelectorAll('.fila-clickable').forEach(function (fila) {
    fila.addEventListener('click', function () {
        const d = JSON.parse(this.dataset.json);

        document.querySelectorAll('.fila-clickable').forEach(f => f.classList.remove('fila-seleccionada'));
        this.classList.add('fila-seleccionada');

        document.getElementById('panelAvatar').textContent = d.iniciales || d.nombre.charAt(0).toUpperCase();
        document.getElementById('panelNombre').textContent = d.nombre;
        document.getElementById('panelBtnVer').href        = d.show_url;

        let html = '';
        html += campo('bi-envelope',    'Email',           d.email);
        html += campo('bi-telephone',   'Teléfono',        d.telefono);
        html += campo('bi-building',    'Institución',     d.institucion);
        html += campo('bi-briefcase',   'Ocupación',       d.ocupacion);
        html += campo('bi-geo-alt',     'Ciudad',          d.ciudad);
        html += campo('bi-share',       'Redes sociales',  d.redes_sociales);
        if (d.eventos !== undefined) {
            html += campo('bi-calendar-check', 'Actividades inscritas',
                `<span class="badge" style="background:#e3f2fd;color:#1565c0;">${d.eventos}</span>`);
        }

        document.getElementById('panelCuerpo').innerHTML = html || '<p class="text-muted small mt-2">Sin información adicional registrada.</p>';

        bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('panelDetalle')).show();
    });
});
</script>
@endsection
