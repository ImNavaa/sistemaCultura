@extends('layouts.app')
@section('title', 'Calendario de Eventos')

@section('content')

{{-- ══ HEADER ══════════════════════════════════════════════ --}}
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon navy"><i class="bi bi-calendar3"></i></div>
        <div>
            <h2>Calendario de Eventos</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Calendario</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalReportePDF">
            <i class="bi bi-file-earmark-pdf me-1"></i>Reporte PDF
        </button>
        @if(auth()->user()->puede('calendario','crear'))
        <button class="btn btn-navy btn-sm" onclick="abrirModal()">
            <i class="bi bi-plus-circle me-1"></i>Nuevo evento
        </button>
        @endif
    </div>
</div>

{{-- ══ BARRA DE NAVEGACIÓN Y LEYENDA ══════════════════════ --}}
<div class="data-card mb-3 p-3">
    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-between">
        {{-- Navegador mes/año --}}
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="window._calendar?.prev()">
                <i class="bi bi-chevron-left"></i>
            </button>
            <select id="selectMes" class="form-select form-select-sm" style="width:130px;" onchange="navegarFecha()">
                @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                <option value="{{ $i }}">{{ $m }}</option>
                @endforeach
            </select>
            <input type="number" id="selectAnio" class="form-control form-control-sm"
                   style="width:85px;" value="{{ date('Y') }}" min="2020" max="2040" onchange="navegarFecha()">
            <button class="btn btn-sm btn-outline-secondary" onclick="window._calendar?.next()">
                <i class="bi bi-chevron-right"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="window._calendar?.today()">
                Hoy
            </button>
        </div>

        {{-- Leyenda --}}
        <div class="d-flex gap-3 flex-wrap align-items-center">
            @foreach([
                ['#3a7bd5','bi-file-earmark-text','Oficio'],
                ['#7b3ad5','bi-receipt','Recibo'],
                ['#e8a020','bi-files','Oficio + Recibo'],
                ['#64748b','bi-dash-circle','Sin documento'],
            ] as [$color,$icon,$label])
            <span class="d-flex align-items-center gap-1" style="font-size:.8rem;color:var(--text-muted);">
                <span style="width:10px;height:10px;border-radius:3px;background:{{ $color }};display:inline-block;flex-shrink:0;"></span>
                {{ $label }}
            </span>
            @endforeach
        </div>
    </div>
</div>

{{-- ══ CALENDARIO ══════════════════════════════════════════ --}}
<div class="data-card">
    <div class="p-3">
        <div id="calendario"></div>
    </div>
</div>

{{-- ══ MODAL REPORTE PDF ═══════════════════════════════════ --}}
<div class="modal fade" id="modalReportePDF" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Reporte PDF</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('calendario.reporte') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Desde</label>
                        <input type="date" name="desde" class="form-control form-control-sm" required id="teatroDesde">
                    </div>
                    <div>
                        <label class="form-label small fw-semibold">Hasta</label>
                        <input type="date" name="hasta" class="form-control form-control-sm" required id="teatroHasta">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Generar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ MODAL CREAR / EDITAR EVENTO ════════════════════════ --}}
<div class="modal fade" id="modalEvento" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header" id="modalHeaderColor" style="background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:9px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-calendar-plus" id="modalHeaderIcon"></i>
                    </div>
                    <h5 class="modal-title mb-0 fw-bold" id="modalTitulo">Nuevo Evento</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <form id="formEvento">
                    @csrf
                    <input type="hidden" id="eventoId">

                    {{-- Sección: Datos generales --}}
                    <div class="p-4 pb-2">
                        <div class="form-section-title mb-3">
                            <i class="bi bi-info-circle me-1"></i>Datos generales
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre del evento <span class="text-danger">*</span></label>
                                <input type="text" id="nombre_evento" name="nombre_evento"
                                       class="form-control" placeholder="Nombre del evento"
                                       list="sugerenciasEventos" required autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Organizador <span class="text-danger">*</span></label>
                                <input type="text" id="organizador" name="organizador"
                                       class="form-control" placeholder="Nombre del organizador" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" id="fecha" name="fecha" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hora inicio</label>
                                <input type="time" id="hora_inicio" name="hora_inicio" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hora fin</label>
                                <input type="time" id="hora_fin" name="hora_fin" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Autoriza</label>
                                <select id="autoriza" name="autoriza" class="form-select">
                                    <option value="">— Seleccionar —</option>
                                    @foreach(['Presidente','Secretario','Finanzas','Dirección de Cultura','Condonado','Se desconoce'] as $op)
                                    <option value="{{ $op }}">{{ $op }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo de documento</label>
                                <select id="tipo" name="tipo" class="form-select" onchange="toggleCampos()">
                                    <option value="ninguno">Sin documento</option>
                                    <option value="oficio">Oficio</option>
                                    <option value="recibo">Recibo</option>
                                    <option value="ambos">Oficio + Recibo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Oficio --}}
                    <div id="camposOficio" class="d-none px-4 pb-2">
                        <div class="rounded p-3 mb-1" style="background:var(--bg-card-alt);border:1px solid #3a7bd530;">
                            <div class="form-section-title mb-3" style="color:#3a7bd5;border-color:#3a7bd530;">
                                <i class="bi bi-file-earmark-text me-1"></i>Datos del Oficio
                            </div>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Número de oficio</label>
                                    <input type="text" id="numero_oficio" name="numero_oficio"
                                           class="form-control" placeholder="OF-2024-001">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Se cobró?</label>
                                    <select id="cobrado" name="cobrado" class="form-select" onchange="toggleMonto()">
                                        <option value="no">No</option>
                                        <option value="si">Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-none" id="montoGroup">
                                    <label class="form-label">Monto ($)</label>
                                    <input type="number" id="monto_cobrado" name="monto_cobrado"
                                           class="form-control" placeholder="0.00" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Recibo --}}
                    <div id="camposRecibo" class="d-none px-4 pb-3">
                        <div class="rounded p-3" style="background:var(--bg-card-alt);border:1px solid #7b3ad530;">
                            <div class="form-section-title mb-3" style="color:#7b3ad5;border-color:#7b3ad530;">
                                <i class="bi bi-receipt me-1"></i>Datos del Recibo
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Número de recibo</label>
                                    <input type="text" id="numero_recibo" name="numero_recibo"
                                           class="form-control" placeholder="REC-2024-001">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Importe ($)</label>
                                    <input type="number" id="importe" name="importe"
                                           class="form-control" placeholder="0.00" step="0.01" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Concepto</label>
                                    <input type="text" id="concepto" name="concepto"
                                           class="form-control" placeholder="Concepto del recibo">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="background:var(--bg-card-alt);">
                <button type="button" class="btn btn-outline-danger me-auto d-none" id="btnEliminar">
                    <i class="bi bi-trash me-1"></i>Eliminar
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-navy" id="btnGuardar" onclick="guardarEvento()">
                    <i class="bi bi-save me-1"></i>Guardar evento
                </button>
            </div>
        </div>
    </div>
</div>

<datalist id="sugerenciasEventos"></datalist>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js"></script>

<style>
/* ── FullCalendar: integración con el sistema de diseño ── */
.fc {
    font-family: 'Segoe UI', system-ui, sans-serif;
    font-size: .875rem;
}
.fc .fc-toolbar-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-main);
    text-transform: capitalize;
}
.fc .fc-button {
    background: var(--bg-card-alt) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-main) !important;
    border-radius: 7px !important;
    font-size: .8rem !important;
    font-weight: 500 !important;
    padding: .3rem .75rem !important;
    box-shadow: none !important;
    transition: all .15s;
}
.fc .fc-button:hover {
    background: var(--bg-row-hover) !important;
}
.fc .fc-button-active,
.fc .fc-button-primary:not(:disabled).fc-button-active {
    background: var(--navy3) !important;
    border-color: var(--navy3) !important;
    color: #fff !important;
}
.fc .fc-col-header-cell {
    background: var(--bg-card-alt);
    padding: .5rem 0;
}
.fc .fc-col-header-cell-cushion {
    font-weight: 600;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--text-muted);
    text-decoration: none !important;
}
.fc .fc-daygrid-day {
    background: var(--bg-card);
    transition: background .15s;
}
.fc .fc-daygrid-day:hover { background: var(--bg-card-alt); }
.fc .fc-daygrid-day.fc-day-today { background: #e8eaf640 !important; }
[data-theme="dark"] .fc .fc-daygrid-day.fc-day-today { background: #1a237e18 !important; }
.fc .fc-daygrid-day-number {
    color: var(--text-muted);
    font-size: .8rem;
    padding: .4rem .5rem;
    text-decoration: none !important;
}
.fc .fc-day-today .fc-daygrid-day-number {
    background: var(--navy3);
    color: #fff;
    border-radius: 50%;
    width: 26px; height: 26px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700;
    margin: .3rem .3rem 0 auto;
    padding: 0;
}
.fc-theme-standard td, .fc-theme-standard th { border-color: var(--border-color) !important; }
.fc-theme-standard .fc-scrollgrid { border-color: var(--border-color) !important; }
.fc .fc-event {
    border: none !important;
    border-radius: 5px !important;
    font-size: .75rem !important;
    padding: 1px 5px !important;
    font-weight: 500;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    transition: opacity .15s, transform .1s;
}
.fc .fc-event:hover { opacity: .88; transform: translateY(-1px); }
.fc .fc-daygrid-event-dot { display: none; }
.fc .fc-list-event:hover td { background: var(--bg-row-hover) !important; }
.fc .fc-list-table { background: var(--bg-card); }
.fc .fc-list-day-cushion { background: var(--bg-card-alt) !important; }
.fc .fc-list-day-text, .fc .fc-list-day-side-text {
    color: var(--text-main) !important;
    text-decoration: none !important;
}

/* Secciones del formulario del modal */
.form-section-title {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text-muted);
    padding-bottom: .5rem;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: .75rem;
}

</style>

<script>
// ── Inicializar fechas del modal de reporte ──────────────
(function(){
    var today    = new Date().toISOString().slice(0,10);
    var firstDay = today.slice(0,8) + '01';
    document.addEventListener('DOMContentLoaded', function(){
        var d = document.getElementById('teatroDesde');
        var h = document.getElementById('teatroHasta');
        if (d) d.value = firstDay;
        if (h) h.value = today;
    });
})();

// ── FullCalendar ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendario');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView:   'dayGridMonth',
        locale:        'es',
        height:        680,
        editable:      true,
        droppable:     true,
        navLinks:      true,
        dayMaxEvents:  3,
        navLinkDayClick: function(date) { calendar.changeView('timeGridDay', date); },
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        buttonText: { today:'Hoy', month:'Mes', week:'Semana', day:'Día', list:'Lista' },
        events: '{{ route("eventos.get") }}',

        dateClick: function(info) { abrirModal(info.dateStr); },
        eventClick: function(info) { abrirModalEditar(info.event); },

        eventDrop: function(info) {
            verificarConflicto(info.event, function(conflicto) {
                if (conflicto && !confirm(`⚠️ Ya existe "${conflicto}" en esa fecha y hora. ¿Continuar?`)) {
                    info.revert(); return;
                }
                actualizarEvento(info.event, info.revert);
            });
        },
        eventResize: function(info) { actualizarEvento(info.event, info.revert); },

        eventMouseEnter: function(info) {
            const p = info.event.extendedProps;
            const horas = p.hora_inicio ? `\n🕐 ${p.hora_inicio}${p.hora_fin ? ' – '+p.hora_fin : ''}` : '';
            info.el.setAttribute('title',
                `👤 ${p.organizador}${p.autoriza ? '\n✅ Autoriza: '+p.autoriza : ''}${horas}\n📄 ${p.tipo}`
            );
        },

        datesSet: function(info) {
            const f = info.view.currentStart;
            document.getElementById('selectMes').value  = f.getMonth();
            document.getElementById('selectAnio').value = f.getFullYear();
        }
    });

    calendar.render();
    window._calendar = calendar;

    // Autocompletar nombres de eventos
    fetch('{{ route("eventos.get") }}')
        .then(r => r.json())
        .then(eventos => {
            const dl = document.getElementById('sugerenciasEventos');
            [...new Set(eventos.map(e => e.title))].forEach(n => {
                const o = document.createElement('option'); o.value = n; dl.appendChild(o);
            });
        });

    // Inicializar selectores de navegación
    const hoy = new Date();
    document.getElementById('selectMes').value  = hoy.getMonth();
    document.getElementById('selectAnio').value = hoy.getFullYear();

    // Validar horas en tiempo real
    ['hora_inicio','hora_fin'].forEach(id =>
        document.getElementById(id).addEventListener('change', validarHoras)
    );
});

// ── Navegación ───────────────────────────────────────────
function navegarFecha() {
    const mes  = parseInt(document.getElementById('selectMes').value);
    const anio = parseInt(document.getElementById('selectAnio').value);
    window._calendar?.gotoDate(new Date(anio, mes, 1));
}

// ── Validaciones ─────────────────────────────────────────
function validarHoras() {
    const inicio = document.getElementById('hora_inicio').value;
    const fin    = document.getElementById('hora_fin').value;
    if (inicio && fin && fin <= inicio) {
        mostrarAlerta('La hora de fin debe ser mayor que la de inicio.', 'warning');
        document.getElementById('hora_fin').value = '';
        document.getElementById('hora_fin').classList.add('is-invalid');
    } else {
        document.getElementById('hora_fin').classList.remove('is-invalid');
    }
}

function validarFormulario() {
    const campos = [
        ['nombre_evento', 'El nombre del evento es obligatorio.'],
        ['organizador',   'El organizador es obligatorio.'],
        ['fecha',         'La fecha es obligatoria.'],
    ];
    for (const [id, msg] of campos) {
        if (!document.getElementById(id).value.trim()) {
            mostrarAlerta(msg, 'danger');
            document.getElementById(id).focus();
            return false;
        }
    }
    const hi = document.getElementById('hora_inicio').value;
    const hf = document.getElementById('hora_fin').value;
    if (hi && hf && hf <= hi) {
        mostrarAlerta('La hora de fin debe ser mayor que la de inicio.', 'danger');
        return false;
    }
    return true;
}

function verificarConflicto(event, callback) {
    const eventos    = window._calendar.getEvents();
    const nuevaFecha = event.startStr.substring(0, 10);
    const nuevaHora  = event.startStr.includes('T') ? event.startStr.substring(11,16) : null;
    const conflicto  = eventos.find(e =>
        e.id !== event.id &&
        e.startStr.substring(0,10) === nuevaFecha &&
        nuevaHora && e.startStr.includes('T') &&
        e.startStr.substring(11,16) === nuevaHora
    );
    callback(conflicto ? conflicto.title : null);
}

function verificarConflictoAlGuardar(callback) {
    const fecha     = document.getElementById('fecha').value;
    const horaI     = document.getElementById('hora_inicio').value;
    const eventoId  = document.getElementById('eventoId').value;
    if (!horaI) { callback(false); return; }
    const conflicto = window._calendar.getEvents().find(e =>
        e.id != eventoId &&
        e.startStr.substring(0,10) === fecha &&
        e.extendedProps.hora_inicio === horaI
    );
    if (conflicto && !confirm(`⚠️ Ya existe "${conflicto.title}" en esa fecha y hora. ¿Guardar de todas formas?`)) {
        callback(true); return;
    }
    callback(false);
}

// ── Modal ─────────────────────────────────────────────────
function abrirModal(fecha = '') {
    resetFormulario();
    document.getElementById('fecha').value     = fecha;
    document.getElementById('modalTitulo').textContent = 'Nuevo Evento';
    document.getElementById('modalHeaderIcon').className = 'bi bi-calendar-plus';
    document.getElementById('btnEliminar').classList.add('d-none');
    document.getElementById('btnGuardar').innerHTML = '<i class="bi bi-save me-1"></i>Guardar evento';
    document.getElementById('btnGuardar').onclick = guardarEvento;
    document.getElementById('btnGuardar').disabled = false;
    toggleCampos();
    new bootstrap.Modal(document.getElementById('modalEvento')).show();
}

function abrirModalEditar(event) {
    const p = event.extendedProps;
    resetFormulario();

    document.getElementById('modalTitulo').textContent     = 'Editar Evento';
    document.getElementById('modalHeaderIcon').className   = 'bi bi-pencil';
    document.getElementById('eventoId').value              = event.id;
    document.getElementById('fecha').value                 = event.startStr.substring(0,10);
    document.getElementById('nombre_evento').value         = event.title;
    document.getElementById('organizador').value           = p.organizador ?? '';
    document.getElementById('autoriza').value              = p.autoriza ?? '';
    document.getElementById('hora_inicio').value           = p.hora_inicio ?? '';
    document.getElementById('hora_fin').value              = p.hora_fin ?? '';
    document.getElementById('tipo').value                  = p.tipo ?? 'ninguno';
    document.getElementById('numero_oficio').value         = p.numero_oficio ?? '';
    document.getElementById('cobrado').value               = p.cobrado ? 'si' : 'no';
    document.getElementById('monto_cobrado').value         = p.monto_cobrado ?? '';
    document.getElementById('numero_recibo').value         = p.numero_recibo ?? '';
    document.getElementById('importe').value               = p.importe ?? '';
    document.getElementById('concepto').value              = p.concepto ?? '';

    toggleCampos();
    if (p.cobrado) document.getElementById('montoGroup').classList.remove('d-none');

    const btnE = document.getElementById('btnEliminar');
    btnE.classList.remove('d-none');
    btnE.onclick = function () {
        if (confirm(`¿Eliminar el evento "${event.title}"?\nEsta acción no se puede deshacer.`)) {
            eliminarEvento(event.id);
            bootstrap.Modal.getInstance(document.getElementById('modalEvento')).hide();
        }
    };

    document.getElementById('btnGuardar').innerHTML = '<i class="bi bi-save me-1"></i>Actualizar evento';
    document.getElementById('btnGuardar').onclick   = actualizarModal;
    document.getElementById('btnGuardar').disabled  = false;

    new bootstrap.Modal(document.getElementById('modalEvento')).show();
}

function resetFormulario() {
    ['eventoId','fecha','nombre_evento','organizador','hora_inicio','hora_fin',
     'numero_oficio','monto_cobrado','numero_recibo','importe','concepto'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('autoriza').value = '';
    document.getElementById('tipo').value     = 'ninguno';
    document.getElementById('cobrado').value  = 'no';
    document.getElementById('montoGroup').classList.add('d-none');
}

// ── CRUD ──────────────────────────────────────────────────
function guardarEvento() {
    if (!validarFormulario()) return;
    verificarConflictoAlGuardar(function(cancelado) {
        if (cancelado) return;
        const btn = document.getElementById('btnGuardar');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch('{{ route("eventos.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: new FormData(document.getElementById('formEvento'))
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEvento')).hide();
                window._calendar.refetchEvents();
                mostrarAlerta('Evento guardado correctamente.', 'success');
            } else {
                mostrarAlerta('Error al guardar el evento.', 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-save me-1"></i>Guardar evento';
            }
        })
        .catch(() => {
            mostrarAlerta('Error de conexión.', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i>Guardar evento';
        });
    });
}

function actualizarModal() {
    if (!validarFormulario()) return;
    verificarConflictoAlGuardar(function(cancelado) {
        if (cancelado) return;
        const btn = document.getElementById('btnGuardar');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Actualizando...';

        const id   = document.getElementById('eventoId').value;
        const data = new FormData(document.getElementById('formEvento'));
        data.append('_method', 'PUT');

        fetch(`/eventos/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: data
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEvento')).hide();
                window._calendar.refetchEvents();
                mostrarAlerta('Evento actualizado correctamente.', 'success');
            } else {
                mostrarAlerta('Error al actualizar.', 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-save me-1"></i>Actualizar evento';
            }
        })
        .catch(() => {
            mostrarAlerta('Error de conexión.', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i>Actualizar evento';
        });
    });
}

function actualizarEvento(event, revert) {
    const start = event.startStr, end = event.endStr;
    fetch(`/eventos/${event.id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            fecha:       start.substring(0,10),
            hora_inicio: start.includes('T') ? start.substring(11,16) : null,
            hora_fin:    end?.includes('T')   ? end.substring(11,16)  : null,
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) mostrarAlerta('Evento movido correctamente.', 'success');
        else revert();
    })
    .catch(() => revert());
}

function eliminarEvento(id) {
    fetch(`/eventos/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window._calendar.refetchEvents();
            mostrarAlerta('Evento eliminado.', 'success');
        }
    });
}

// ── UI helpers ────────────────────────────────────────────
function toggleCampos() {
    const tipo = document.getElementById('tipo').value;
    document.getElementById('camposOficio').classList.toggle('d-none', !['oficio','ambos'].includes(tipo));
    document.getElementById('camposRecibo').classList.toggle('d-none', !['recibo','ambos'].includes(tipo));
}

function toggleMonto() {
    document.getElementById('montoGroup').classList.toggle('d-none',
        document.getElementById('cobrado').value !== 'si');
}

function mostrarAlerta(mensaje, tipo = 'info') {
    const icons = { success:'check-circle', danger:'x-circle', warning:'exclamation-triangle', info:'info-circle' };
    const div = document.createElement('div');
    div.className = `alert alert-${tipo} alert-dismissible fade show position-fixed d-flex align-items-center gap-2`;
    div.style.cssText = 'top:1.25rem;right:1.25rem;z-index:9999;min-width:300px;max-width:420px;box-shadow:0 4px 20px rgba(0,0,0,.18);border-radius:10px;';
    div.innerHTML = `<i class="bi bi-${icons[tipo]??'info-circle'} flex-shrink-0"></i>
        <span class="flex-grow-1">${mensaje}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 4000);
}
</script>
@endsection
