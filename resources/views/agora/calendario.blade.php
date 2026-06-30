@extends('layouts.app')

@section('title', 'Ágora — Calendario de Reservas')

@push('styles')
{{-- FullCalendar --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
/* ── Layout ── */
.agora-wrap { display: flex; gap: 1rem; align-items: flex-start; }
.agora-sidebar {
    width: 230px;
    flex-shrink: 0;
    position: sticky;
    top: 76px;
}
.agora-cal { flex: 1; min-width: 0; }

@media (max-width: 768px) {
    .agora-wrap { flex-direction: column; }
    .agora-sidebar { width: 100%; position: static; }
}

/* ── Sidebar ── */
.sidebar-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: .75rem;
}
.sidebar-card h6 { font-size: .78rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--text-muted); margin-bottom: .6rem; }

.tipo-legend { display: flex; align-items: center; gap: .5rem; font-size: .82rem; padding: .25rem 0; }
.tipo-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

.area-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .75rem; padding: .2em .55em;
    border-radius: 6px; margin: .15rem;
    border: 1px solid transparent;
    cursor: pointer;
    transition: opacity .15s;
}
.area-chip.inactive { opacity: .4; }

/* ── FullCalendar overrides ── */
.fc { font-family: inherit !important; }
.fc .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 700 !important; color: var(--text-main) !important; }
.fc .fc-button { font-size: .8rem !important; border-radius: 8px !important; }
.fc .fc-button-primary { background: var(--accent, #3a7bd5) !important; border-color: var(--accent, #3a7bd5) !important; }
.fc .fc-button-primary:hover { filter: brightness(.9); }
.fc .fc-button-active { filter: brightness(.85) !important; }
.fc .fc-daygrid-day-number, .fc .fc-col-header-cell-cushion { color: var(--text-main) !important; }
.fc .fc-day-today { background: color-mix(in srgb, var(--accent,#3a7bd5) 8%, transparent) !important; }
.fc-theme-standard td, .fc-theme-standard th, .fc-theme-standard .fc-scrollgrid { border-color: var(--border-color) !important; }
.fc .fc-daygrid-body, .fc .fc-scrollgrid { background: var(--bg-card) !important; }
.fc .fc-event { cursor: pointer; border-radius: 5px !important; font-size: .75rem !important; padding: 1px 4px !important; }
.fc-event-title { font-weight: 600 !important; }

/* ── Estado badges en tooltip ── */
.estado-tentativo { background: rgba(245,158,11,.12); color: #b45309; border:1px solid rgba(245,158,11,.3); border-radius:5px; padding:.1em .45em; font-size:.7rem; font-weight:600; }
.estado-cancelado  { background: rgba(107,114,128,.12); color: #6b7280; border:1px solid rgba(107,114,128,.3); border-radius:5px; padding:.1em .45em; font-size:.7rem; font-weight:600; }

/* ── Popup de detalle ── */
#agoraPopup {
    position: fixed;
    z-index: 1100;
    min-width: 260px;
    max-width: 320px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
    padding: 1rem;
    display: none;
}
#agoraPopup .pop-header {
    display: flex; align-items: flex-start; justify-content: space-between; gap: .5rem; margin-bottom: .5rem;
}
#agoraPopup .pop-titulo { font-weight: 700; font-size: .92rem; line-height: 1.3; }
#agoraPopup .pop-row { display: flex; gap: .4rem; font-size: .78rem; color: var(--text-muted); margin-bottom: .2rem; align-items: flex-start; }
#agoraPopup .pop-row i { margin-top: .1rem; flex-shrink: 0; }
#agoraPopup .pop-actions { display: flex; gap: .5rem; margin-top: .75rem; padding-top: .6rem; border-top: 1px solid var(--border-color); }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- Encabezado --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-building me-2"></i>Ágora — Reservas</h4>
            <p class="small mb-0" style="color:var(--text-muted)">Calendario de eventos, fotografías y uso de áreas</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->puede('agora','editar'))
            <a href="{{ route('agora.areas') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-grid-3x3-gap me-1"></i>Gestionar Áreas
            </a>
            @endif
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalReporteAgora">
                <i class="bi bi-file-earmark-pdf me-1"></i>Reporte PDF
            </button>
            @if(auth()->user()->puede('agora','crear'))
            <button class="btn btn-primary btn-sm px-3" id="btnNuevaReserva">
                <i class="bi bi-plus-lg me-1"></i>Nueva Reserva
            </button>
            @endif
        </div>
    </div>

    <div class="agora-wrap">

        {{-- ── Sidebar ── --}}
        <aside class="agora-sidebar">

            {{-- Leyenda de tipos --}}
            <div class="sidebar-card">
                <h6><i class="bi bi-circle-fill me-1"></i>Tipos de reserva</h6>
                <div class="tipo-legend">
                    <span class="tipo-dot" style="background:#6366f1"></span>
                    <span>Evento completo</span>
                </div>
                <div class="tipo-legend">
                    <span class="tipo-dot" style="background:#10b981"></span>
                    <span>Sesión fotográfica</span>
                </div>
                <div class="tipo-legend">
                    <span class="tipo-dot" style="background:#f59e0b"></span>
                    <span>Área específica</span>
                </div>
                <div class="tipo-legend mt-1" style="border-top:1px solid var(--border-color); padding-top:.4rem">
                    <span class="tipo-dot" style="background:#9ca3af"></span>
                    <span style="color:var(--text-muted)">Cancelado</span>
                </div>
            </div>

            {{-- Áreas (filtro visual) --}}
            @if($areas->count())
            <div class="sidebar-card">
                <h6><i class="bi bi-grid-3x3-gap me-1"></i>Áreas del Ágora</h6>
                <div id="filtroAreas">
                    @foreach($areas as $area)
                    <span class="area-chip" data-area-id="{{ $area->id }}"
                          style="background:{{ $area->color }}22; color:{{ $area->color }}; border-color:{{ $area->color }}44">
                        <span class="tipo-dot" style="background:{{ $area->color }}; width:7px; height:7px;"></span>
                        {{ $area->nombre }}
                        @if($area->capacidad)
                        <span style="font-size:.65rem; opacity:.7">({{ $area->capacidad }})</span>
                        @endif
                    </span>
                    @endforeach
                </div>
                <p class="mt-2 mb-0" style="font-size:.7rem; color:var(--text-muted)">Clic para filtrar reservas por área</p>
            </div>
            @else
            <div class="sidebar-card">
                <h6><i class="bi bi-grid-3x3-gap me-1"></i>Áreas del Ágora</h6>
                <p class="small mb-0" style="color:var(--text-muted)">
                    Aún no hay áreas configuradas.
                    @if(auth()->user()->puede('agora','editar'))
                    <a href="{{ route('agora.areas') }}">Agregar áreas</a>
                    @endif
                </p>
            </div>
            @endif

            {{-- Mini stats del mes actual --}}
            <div class="sidebar-card">
                <h6><i class="bi bi-bar-chart me-1"></i>Este mes</h6>
                <div id="statsmes" style="font-size:.8rem; color:var(--text-muted)">Cargando…</div>
            </div>

        </aside>

        {{-- ── Calendario ── --}}
        <div class="agora-cal">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2 p-md-3">
                    <div id="agoraCalendario"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Popup de detalle de evento --}}
<div id="agoraPopup">
    <div class="pop-header">
        <div>
            <div id="popTipoBadge" class="mb-1"></div>
            <div class="pop-titulo" id="popTitulo"></div>
        </div>
        <button class="btn btn-sm btn-link p-0 text-muted" id="popCerrar"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="pop-row"><i class="bi bi-calendar2"></i><span id="popFecha"></span></div>
    <div class="pop-row" id="popHoraRow"><i class="bi bi-clock"></i><span id="popHora"></span></div>
    <div class="pop-row"><i class="bi bi-person"></i><span id="popOrganizador"></span></div>
    <div class="pop-row" id="popResponsableRow"><i class="bi bi-person-badge"></i><span id="popResponsable"></span></div>
    <div class="pop-row" id="popTelRow"><i class="bi bi-telephone"></i><span id="popTel"></span></div>
    <div class="pop-row" id="popAreasRow"><i class="bi bi-grid-3x3-gap"></i><span id="popAreas"></span></div>
    <div class="pop-row" id="popDescRow"><i class="bi bi-text-left"></i><span id="popDesc"></span></div>
    <div id="popEstadoRow" class="mb-1"></div>
    <div class="pop-actions">
        @if(auth()->user()->puede('agora','editar'))
        <button class="btn btn-sm btn-outline-primary flex-fill" id="popBtnEditar">
            <i class="bi bi-pencil me-1"></i>Editar
        </button>
        @endif
        @if(auth()->user()->puede('agora','eliminar'))
        <button class="btn btn-sm btn-outline-danger" id="popBtnEliminar" title="Eliminar">
            <i class="bi bi-trash"></i>
        </button>
        @endif
    </div>
</div>

{{-- ══ MODAL NUEVA / EDITAR RESERVA ══ --}}
<div class="modal fade" id="modalReserva" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalReservaTitulo">Nueva Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <input type="hidden" id="reservaId">

                {{-- Tipo (tabs visuales) --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Tipo de reserva</label>
                    <div class="d-flex gap-2" id="tipoSelector">
                        <label class="tipo-btn flex-fill text-center p-2 rounded-3 border" style="cursor:pointer" data-tipo="evento">
                            <input type="radio" name="tipo" value="evento" class="visually-hidden">
                            <i class="bi bi-film d-block mb-1" style="font-size:1.2rem;color:#6366f1"></i>
                            <span style="font-size:.75rem; font-weight:600">Evento</span>
                        </label>
                        <label class="tipo-btn flex-fill text-center p-2 rounded-3 border" style="cursor:pointer" data-tipo="fotografia">
                            <input type="radio" name="tipo" value="fotografia" class="visually-hidden">
                            <i class="bi bi-camera d-block mb-1" style="font-size:1.2rem;color:#10b981"></i>
                            <span style="font-size:.75rem; font-weight:600">Fotografía</span>
                        </label>
                        <label class="tipo-btn flex-fill text-center p-2 rounded-3 border" style="cursor:pointer" data-tipo="area">
                            <input type="radio" name="tipo" value="area" class="visually-hidden">
                            <i class="bi bi-grid-3x3-gap d-block mb-1" style="font-size:1.2rem;color:#f59e0b"></i>
                            <span style="font-size:.75rem; font-weight:600">Área</span>
                        </label>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Título / Nombre del evento <span class="text-danger">*</span></label>
                        <input type="text" id="rTitulo" class="form-control" placeholder="Nombre del evento o sesión">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Organizador <span class="text-danger">*</span></label>
                        <input type="text" id="rOrganizador" class="form-control" placeholder="Nombre">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Responsable en sitio</label>
                        <input type="text" id="rResponsable" class="form-control" placeholder="Opcional">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Teléfono de contacto</label>
                        <input type="text" id="rTelefono" class="form-control" placeholder="Opcional">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Estado</label>
                        <select id="rEstado" class="form-select form-select-sm">
                            <option value="confirmado">✅ Confirmado</option>
                            <option value="tentativo">⏳ Tentativo</option>
                            <option value="cancelado">❌ Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold small">Fecha <span class="text-danger">*</span></label>
                        <input type="date" id="rFecha" class="form-control">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold small">Hora inicio</label>
                        <input type="time" id="rHoraInicio" class="form-control">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold small">Hora fin</label>
                        <input type="time" id="rHoraFin" class="form-control">
                    </div>
                </div>

                {{-- Áreas (solo si tipo = area) --}}
                @if($areas->count())
                <div id="seccionAreas" class="mb-2" style="display:none">
                    <label class="form-label fw-semibold small">Áreas a usar <span class="text-danger">*</span></label>
                    <div class="border rounded-3 p-2" style="max-height:150px; overflow-y:auto">
                        @foreach($areas as $area)
                        <div class="form-check mb-1">
                            <input class="form-check-input area-check" type="checkbox"
                                   id="areaCheck_{{ $area->id }}" value="{{ $area->id }}">
                            <label class="form-check-label small" for="areaCheck_{{ $area->id }}">
                                <span class="rounded-circle d-inline-block me-1"
                                      style="width:8px;height:8px;background:{{ $area->color }}"></span>
                                {{ $area->nombre }}
                                @if($area->capacidad)
                                <small class="text-muted">(cap. {{ $area->capacidad }})</small>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mb-2">
                    <label class="form-label fw-semibold small">Descripción</label>
                    <textarea id="rDescripcion" class="form-control" rows="2" placeholder="Detalles del evento…"></textarea>
                </div>

                @if(auth()->user()->puede('agora','editar'))
                <div>
                    <label class="form-label fw-semibold small">Notas internas</label>
                    <textarea id="rNotas" class="form-control" rows="2" placeholder="Notas solo para el equipo…"></textarea>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm px-4" id="btnGuardarReserva">
                    <i class="bi bi-check-lg me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js"></script>

<script>
(function () {
    var csrf = '{{ csrf_token() }}';
    var puedeCrear  = {{ auth()->user()->puede('agora','crear')   ? 'true' : 'false' }};
    var puedeEditar = {{ auth()->user()->puede('agora','editar')  ? 'true' : 'false' }};
    var puedeEliminar = {{ auth()->user()->puede('agora','eliminar') ? 'true' : 'false' }};

    var calendarioFC = null;
    var reservaActualId = null;
    var tipoSeleccionado = 'evento';
    var filtroAreaActivo = null;

    // ── Colores de tipo ──────────────────────────────────────
    var coloresTipo = { evento: '#6366f1', fotografia: '#10b981', area: '#f59e0b' };
    var etiquetasTipo = { evento: 'Evento', fotografia: 'Sesión Fotográfica', area: 'Área Específica' };

    // ── FullCalendar ─────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {

        calendarioFC = new FullCalendar.Calendar(document.getElementById('agoraCalendario'), {
            locale: 'es',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,listMonth',
            },
            height: 'auto',
            editable: puedeEditar,
            eventStartEditable: puedeEditar,
            selectable: puedeCrear,

            events: function (fetchInfo, success, failure) {
                fetch('{{ route("agora.reservas.get") }}', {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(function(r) {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function (data) {
                        if (data && data.error) {
                            failure(new Error(data.message || 'Error al cargar reservas'));
                            return;
                        }
                        // Filtrar por área si hay filtro activo
                        if (filtroAreaActivo) {
                            data = data.filter(function(e) {
                                var ids = e.extendedProps.areas_ids || [];
                                return ids.includes(parseInt(filtroAreaActivo));
                            });
                        }
                        success(data);
                        actualizarStats(data);
                    })
                    .catch(failure);
            },

            // Click en fecha vacía → abrir modal con esa fecha
            dateClick: function (info) {
                if (!puedeCrear) return;
                abrirModalNuevo(info.dateStr);
            },

            // Click en evento → mostrar popup
            eventClick: function (info) {
                info.jsEvent.stopPropagation();
                mostrarPopup(info.event, info.jsEvent);
            },

            // Drag & drop
            eventDrop: function (info) {
                if (!puedeEditar) { info.revert(); return; }
                var id = info.event.id;
                var startStr = info.event.startStr;
                var fecha = startStr.substring(0, 10);
                var horaInicio = startStr.length > 10 ? startStr.substring(11, 16) : null;
                var endStr = info.event.endStr;
                var horaFin = endStr && endStr.length > 10 ? endStr.substring(11, 16) : null;

                fetch('/agora/reservas/' + id + '/mover', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ fecha: fecha, hora_inicio: horaInicio, hora_fin: horaFin }),
                })
                .then(r => r.json())
                .then(function (d) { if (!d.success) info.revert(); })
                .catch(function () { info.revert(); });
            },
        });

        calendarioFC.render();

        // ── Selección de tipo en modal ──────────────────────
        document.querySelectorAll('.tipo-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                seleccionarTipo(this.dataset.tipo);
            });
        });
        seleccionarTipo('evento');

        // ── Botón nueva reserva ─────────────────────────────
        document.getElementById('btnNuevaReserva')?.addEventListener('click', function () {
            abrirModalNuevo(null);
        });

        // ── Guardar reserva ─────────────────────────────────
        document.getElementById('btnGuardarReserva').addEventListener('click', guardarReserva);

        // ── Popup cerrar / clic fuera ───────────────────────
        document.getElementById('popCerrar').addEventListener('click', cerrarPopup);
        document.addEventListener('click', function (e) {
            var popup = document.getElementById('agoraPopup');
            if (popup.style.display !== 'none' && !popup.contains(e.target)) cerrarPopup();
        });

        // ── Filtro de áreas (sidebar) ──────────────────────
        document.querySelectorAll('#filtroAreas .area-chip').forEach(function (chip) {
            chip.addEventListener('click', function () {
                var id = this.dataset.areaId;
                if (filtroAreaActivo === id) {
                    filtroAreaActivo = null;
                    document.querySelectorAll('.area-chip').forEach(c => c.classList.remove('inactive'));
                } else {
                    filtroAreaActivo = id;
                    document.querySelectorAll('.area-chip').forEach(function (c) {
                        c.classList.toggle('inactive', c.dataset.areaId !== id);
                    });
                }
                calendarioFC.refetchEvents();
            });
        });
    });

    // ── Funciones de modal ───────────────────────────────────

    function abrirModalNuevo(fecha) {
        reservaActualId = null;
        document.getElementById('modalReservaTitulo').textContent = 'Nueva Reserva';
        limpiarModal();
        if (fecha) document.getElementById('rFecha').value = fecha;
        seleccionarTipo('evento');
        var modal = new bootstrap.Modal(document.getElementById('modalReserva'));
        modal.show();
    }

    function abrirModalEditar(props, id) {
        reservaActualId = id;
        document.getElementById('modalReservaTitulo').textContent = 'Editar Reserva';
        document.getElementById('rTitulo').value      = props.titulo ?? props.title ?? '';
        document.getElementById('rOrganizador').value = props.organizador ?? '';
        document.getElementById('rResponsable').value = props.responsable ?? '';
        document.getElementById('rTelefono').value    = props.telefono ?? '';
        document.getElementById('rFecha').value       = props.fecha ?? '';
        document.getElementById('rHoraInicio').value  = props.hora_inicio ?? '';
        document.getElementById('rHoraFin').value     = props.hora_fin ?? '';
        document.getElementById('rDescripcion').value = props.descripcion ?? '';
        document.getElementById('rNotas').value       = props.notas ?? '';
        document.getElementById('rEstado').value      = props.estado ?? 'confirmado';
        seleccionarTipo(props.tipo ?? 'evento');

        // Marcar áreas
        document.querySelectorAll('.area-check').forEach(function (cb) {
            cb.checked = (props.areas_ids || []).includes(parseInt(cb.value));
        });

        cerrarPopup();
        var modal = new bootstrap.Modal(document.getElementById('modalReserva'));
        modal.show();
    }

    function seleccionarTipo(tipo) {
        tipoSeleccionado = tipo;
        document.querySelectorAll('.tipo-btn').forEach(function (btn) {
            var activo = btn.dataset.tipo === tipo;
            btn.style.background    = activo ? coloresTipo[tipo] + '18' : '';
            btn.style.borderColor   = activo ? coloresTipo[tipo] : 'var(--border-color)';
            btn.style.color         = activo ? coloresTipo[tipo] : '';
            btn.querySelector('input').checked = activo;
        });
        var secAreas = document.getElementById('seccionAreas');
        if (secAreas) secAreas.style.display = tipo === 'area' ? '' : 'none';
    }

    function limpiarModal() {
        ['rTitulo','rOrganizador','rResponsable','rTelefono','rFecha',
         'rHoraInicio','rHoraFin','rDescripcion','rNotas'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.value = '';
        });
        document.getElementById('rEstado').value = 'confirmado';
        document.querySelectorAll('.area-check').forEach(cb => cb.checked = false);
    }

    function guardarReserva() {
        var titulo = document.getElementById('rTitulo').value.trim();
        var org    = document.getElementById('rOrganizador').value.trim();
        var fecha  = document.getElementById('rFecha').value;

        if (!titulo || !org || !fecha) {
            alert('Título, organizador y fecha son obligatorios.');
            return;
        }

        var areasIds = [];
        document.querySelectorAll('.area-check:checked').forEach(function (cb) {
            areasIds.push(parseInt(cb.value));
        });

        var payload = {
            titulo:             titulo,
            tipo:               tipoSeleccionado,
            organizador:        org,
            responsable:        document.getElementById('rResponsable').value.trim() || null,
            telefono_contacto:  document.getElementById('rTelefono').value.trim() || null,
            fecha:              fecha,
            hora_inicio:        document.getElementById('rHoraInicio').value || null,
            hora_fin:           document.getElementById('rHoraFin').value    || null,
            areas_ids:          areasIds.length ? areasIds : null,
            descripcion:        document.getElementById('rDescripcion').value.trim() || null,
            notas_internas:     document.getElementById('rNotas')?.value.trim() || null,
            estado:             document.getElementById('rEstado').value,
        };

        var url    = reservaActualId ? '/agora/reservas/' + reservaActualId : '/agora/reservas';
        var method = reservaActualId ? 'PUT' : 'POST';

        var btn = document.getElementById('btnGuardarReserva');
        btn.disabled = true;

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(function (d) {
            btn.disabled = false;
            if (d.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalReserva'))?.hide();
                calendarioFC.refetchEvents();
            } else {
                alert(d.message || 'Error al guardar.');
            }
        })
        .catch(function () { btn.disabled = false; alert('Error de red.'); });
    }

    // ── Popup de detalle ─────────────────────────────────────

    function mostrarPopup(event, jsEvent) {
        var p  = event.extendedProps;
        var id = event.id;

        document.getElementById('popTitulo').textContent = event.title;

        var tipo  = p.tipo ?? 'evento';
        var color = coloresTipo[tipo] ?? '#6366f1';
        document.getElementById('popTipoBadge').innerHTML =
            '<span style="font-size:.7rem;font-weight:700;padding:.2em .55em;border-radius:6px;background:' + color + '22;color:' + color + ';border:1px solid ' + color + '44">' +
            (etiquetasTipo[tipo] ?? tipo) + '</span>' +
            (p.estado && p.estado !== 'confirmado' ?
                ' <span class="estado-' + p.estado + '">' + (p.estado === 'tentativo' ? 'Tentativo' : 'Cancelado') + '</span>' : '');

        // Fecha y hora
        var meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
        var d = new Date(p.fecha + 'T12:00:00');
        document.getElementById('popFecha').textContent = d.getDate() + ' ' + meses[d.getMonth()] + ' ' + d.getFullYear();

        var horaRow = document.getElementById('popHoraRow');
        if (p.hora_inicio) {
            document.getElementById('popHora').textContent = p.hora_inicio + (p.hora_fin ? ' – ' + p.hora_fin : '');
            horaRow.style.display = '';
        } else { horaRow.style.display = 'none'; }

        document.getElementById('popOrganizador').textContent = p.organizador;

        var respRow = document.getElementById('popResponsableRow');
        if (p.responsable) {
            document.getElementById('popResponsable').textContent = p.responsable;
            respRow.style.display = '';
        } else { respRow.style.display = 'none'; }

        var telRow = document.getElementById('popTelRow');
        if (p.telefono) {
            document.getElementById('popTel').textContent = p.telefono;
            telRow.style.display = '';
        } else { telRow.style.display = 'none'; }

        var areasRow = document.getElementById('popAreasRow');
        if (p.areas && p.areas.length) {
            document.getElementById('popAreas').textContent = p.areas.join(', ');
            areasRow.style.display = '';
        } else { areasRow.style.display = 'none'; }

        var descRow = document.getElementById('popDescRow');
        if (p.descripcion) {
            document.getElementById('popDesc').textContent = p.descripcion;
            descRow.style.display = '';
        } else { descRow.style.display = 'none'; }

        // Botones acción
        var btnEditar   = document.getElementById('popBtnEditar');
        var btnEliminar = document.getElementById('popBtnEliminar');

        if (btnEditar) {
            btnEditar.onclick = function () {
                abrirModalEditar(Object.assign({titulo: event.title}, p), id);
            };
        }
        if (btnEliminar) {
            btnEliminar.onclick = function () {
                if (!confirm('¿Eliminar esta reserva?')) return;
                fetch('/agora/reservas/' + id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf },
                })
                .then(r => r.json())
                .then(function (d) {
                    if (d.success) { cerrarPopup(); calendarioFC.refetchEvents(); }
                });
            };
        }

        // Posicionar popup
        var popup = document.getElementById('agoraPopup');
        popup.style.display = 'block';
        var x = jsEvent.clientX + 12;
        var y = jsEvent.clientY + 12;
        if (x + 330 > window.innerWidth)  x = jsEvent.clientX - 330;
        if (y + 320 > window.innerHeight) y = jsEvent.clientY - 320;
        popup.style.left = x + 'px';
        popup.style.top  = (y + window.scrollY) + 'px';
    }

    function cerrarPopup() {
        document.getElementById('agoraPopup').style.display = 'none';
    }

    // ── Stats del mes ────────────────────────────────────────
    function actualizarStats(eventos) {
        var hoy = new Date();
        var mes = hoy.getMonth(); var anio = hoy.getFullYear();
        var cont = { evento: 0, fotografia: 0, area: 0 };

        eventos.forEach(function (e) {
            var d = new Date(e.start);
            if (d.getMonth() === mes && d.getFullYear() === anio && e.extendedProps.estado !== 'cancelado') {
                var t = e.extendedProps.tipo || 'evento';
                if (cont[t] !== undefined) cont[t]++;
            }
        });

        var html = '';
        if (cont.evento)     html += '<div>📅 <strong>' + cont.evento + '</strong> evento' + (cont.evento>1?'s':'') + '</div>';
        if (cont.fotografia) html += '<div>📷 <strong>' + cont.fotografia + '</strong> sesión fotográfica' + (cont.fotografia>1?'s':'') + '</div>';
        if (cont.area)       html += '<div>📐 <strong>' + cont.area + '</strong> reserva de área' + (cont.area>1?'s':'') + '</div>';
        if (!html) html = '<span style="font-size:.75rem">Sin reservas este mes</span>';

        document.getElementById('statsmes').innerHTML = html;
    }

})();
</script>

{{-- Modal rango de fechas para reporte Ágora --}}
<div class="modal fade" id="modalReporteAgora" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header py-3">
        <h6 class="modal-title"><i class="bi bi-file-earmark-pdf me-2"></i>Reporte PDF — Ágora</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('agora.reporte') }}" method="GET" target="_blank">
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Desde</label>
            <input type="date" name="desde" class="form-control form-control-sm" required id="agoraDesde">
          </div>
          <div class="mb-1">
            <label class="form-label small fw-semibold">Hasta</label>
            <input type="date" name="hasta" class="form-control form-control-sm" required id="agoraHasta">
          </div>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i>Generar PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
(function(){
  var today = new Date().toISOString().slice(0,10);
  var firstDay = today.slice(0,8) + '01';
  document.addEventListener('DOMContentLoaded', function(){
    var d = document.getElementById('agoraDesde');
    var h = document.getElementById('agoraHasta');
    if (d) d.value = firstDay;
    if (h) h.value = today;
  });
})();
</script>
@endsection
