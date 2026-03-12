@extends('layouts.app')

@section('title', 'Calendario de Eventos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar3"></i> Calendario de Eventos</h2>
    <button class="btn btn-primary" onclick="abrirModal()">
        <i class="bi bi-plus-circle"></i> Nuevo Evento
    </button>
</div>

{{-- Navegador de mes/año --}}
<div class="d-flex gap-2 mb-3 align-items-center flex-wrap">
    <select id="selectMes" class="form-select form-select-sm" style="width:140px" onchange="navegarFecha()">
        <option value="0">Enero</option>
        <option value="1">Febrero</option>
        <option value="2">Marzo</option>
        <option value="3">Abril</option>
        <option value="4">Mayo</option>
        <option value="5">Junio</option>
        <option value="6">Julio</option>
        <option value="7">Agosto</option>
        <option value="8">Septiembre</option>
        <option value="9">Octubre</option>
        <option value="10">Noviembre</option>
        <option value="11">Diciembre</option>
    </select>
    <input type="number" id="selectAnio" class="form-control form-select-sm"
        style="width:90px" value="{{ date('Y') }}" min="2020" max="2040">
    <button class="btn btn-sm btn-outline-secondary" onclick="navegarFecha()">
        <i class="bi bi-search"></i> Ir
    </button>

    <div class="ms-auto d-flex gap-3 flex-wrap">
        <span><span style="background:#3a7bd5" class="badge">&nbsp;</span> Oficio</span>
        <span><span style="background:#7b3ad5" class="badge">&nbsp;</span> Recibo</span>
        <span><span style="background:#e8c547" class="badge">&nbsp;</span> Oficio + Recibo</span>
        <span><span style="background:#555" class="badge">&nbsp;</span> Sin documento</span>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div id="calendario"></div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalEvento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitulo">Nuevo Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEvento">
                    @csrf
                    <input type="hidden" id="eventoId" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" id="fecha" name="fecha" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Autoriza</label>
                            <input type="text" id="autoriza" name="autoriza" class="form-control"
                                placeholder="Nombre de quien autoriza">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Organizador <span class="text-danger">*</span></label>
                            <input type="text" id="organizador" name="organizador" class="form-control"
                                placeholder="Nombre del organizador" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora Inicio</label>
                            <input type="time" id="hora_inicio" name="hora_inicio" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora Fin</label>
                            <input type="time" id="hora_fin" name="hora_fin" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Nombre del Evento <span class="text-danger">*</span></label>
                            <input type="text" id="nombre_evento" name="nombre_evento" class="form-control"
                                placeholder="Nombre del evento" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Tipo de Documento</label>
                            <select id="tipo" name="tipo" class="form-select" onchange="toggleCampos()">
                                <option value="ninguno">Sin documento</option>
                                <option value="oficio">Oficio</option>
                                <option value="recibo">Recibo</option>
                                <option value="ambos">Oficio + Recibo</option>
                            </select>
                        </div>

                        <!-- Campos Oficio -->
                        <div id="camposOficio" class="col-12 d-none">
                            <div class="card bg-light border-primary">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="bi bi-file-earmark-text"></i> Datos del Oficio
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Número de Oficio</label>
                                            <input type="text" id="numero_oficio" name="numero_oficio"
                                                class="form-control" placeholder="OF-2024-001">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">¿Se cobró?</label>
                                            <select id="cobrado" name="cobrado" class="form-select"
                                                onchange="toggleMonto()">
                                                <option value="no">No</option>
                                                <option value="si">Sí</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-none" id="montoGroup">
                                            <label class="form-label">Monto ($)</label>
                                            <input type="number" id="monto_cobrado" name="monto_cobrado"
                                                class="form-control" placeholder="0.00" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos Recibo -->
                        <!-- Campos Recibo -->
                        <div id="camposRecibo" class="col-12 d-none">
                            <div class="card bg-light" style="border-color:#7b3ad5!important; border:1px solid">
                                <div class="card-body">
                                    <h6 style="color:#7b3ad5" class="mb-3"><i class="bi bi-receipt"></i> Datos del Recibo</h6>
                                    <div class="row g-3">

                                        <div class="col-md-4"> {{-- NUEVO --}}
                                            <label class="form-label">Número de Recibo</label>
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
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto d-none" id="btnEliminar">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardar" onclick="guardarEvento()">
                    <i class="bi bi-save"></i> Guardar Evento
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendario');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            height: 650,
            editable: true,
            droppable: true,
            navLinks: true,
            navLinkDayClick: function(date) {
                calendar.changeView('timeGridDay', date);
            },
            navLinkWeekClick: function(weekStart) {
                calendar.changeView('timeGridWeek', weekStart);
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista'
            },
            events: '{{ route("eventos.get") }}',

            dateClick: function(info) {
                abrirModal(info.dateStr);
            },

            eventClick: function(info) {
                abrirModalEditar(info.event);
            },

            eventDrop: function(info) {
                actualizarEvento(info.event, info.revert);
            },

            eventResize: function(info) {
                actualizarEvento(info.event, info.revert);
            },

            eventMouseEnter: function(info) {
                const p = info.event.extendedProps;
                info.el.setAttribute('title',
                    `Organizador: ${p.organizador}\nTipo: ${p.tipo}`
                );
            },

            // Sincroniza selectores cuando el calendario navega
            datesSet: function(info) {
                const fecha = info.view.currentStart;
                document.getElementById('selectMes').value = fecha.getMonth();
                document.getElementById('selectAnio').value = fecha.getFullYear();
            }
        });

        calendar.render();
        window._calendar = calendar;

        // Inicializa selectores con el mes actual
        const hoy = new Date();
        document.getElementById('selectMes').value = hoy.getMonth();
        document.getElementById('selectAnio').value = hoy.getFullYear();
    });

    // Navegar a mes/año seleccionado
    function navegarFecha() {
        const mes = parseInt(document.getElementById('selectMes').value);
        const anio = parseInt(document.getElementById('selectAnio').value);
        window._calendar.gotoDate(new Date(anio, mes, 1));
    }

    // Abre modal para CREAR
    function abrirModal(fecha = '') {
        document.getElementById('modalTitulo').textContent = 'Nuevo Evento';
        document.getElementById('eventoId').value = '';
        document.getElementById('fecha').value = fecha;
        document.getElementById('numero_recibo').value = '';
        document.getElementById('autoriza').value = '';
        document.getElementById('nombre_evento').value = '';
        document.getElementById('organizador').value = '';
        document.getElementById('hora_inicio').value = '';
        document.getElementById('hora_fin').value = '';
        document.getElementById('tipo').value = 'ninguno';
        document.getElementById('btnGuardar').textContent = 'Guardar Evento';
        document.getElementById('btnGuardar').onclick = guardarEvento;
        document.getElementById('btnEliminar').classList.add('d-none');
        toggleCampos();
        new bootstrap.Modal(document.getElementById('modalEvento')).show();
    }

    // Abre modal para EDITAR
    function abrirModalEditar(event) {
        const p = event.extendedProps;

        document.getElementById('modalTitulo').textContent = 'Editar Evento';
        document.getElementById('eventoId').value = event.id;
        document.getElementById('fecha').value = event.startStr.substring(0, 10);
        document.getElementById('numero_recibo').value = p.numero_recibo ?? '';
        document.getElementById('nombre_evento').value = event.title;
        document.getElementById('organizador').value = p.organizador ?? '';
        document.getElementById('autoriza').value = p.autoriza ?? '';
        document.getElementById('hora_inicio').value = p.hora_inicio ?? '';
        document.getElementById('hora_fin').value = p.hora_fin ?? '';
        document.getElementById('tipo').value = p.tipo ?? 'ninguno';
        document.getElementById('btnGuardar').textContent = 'Actualizar Evento';
        document.getElementById('btnGuardar').onclick = actualizarModal;

        // Muestra botón eliminar
        const btnEliminar = document.getElementById('btnEliminar');
        btnEliminar.classList.remove('d-none');
        btnEliminar.onclick = function() {
            if (confirm(`¿Eliminar el evento "${event.title}"?`)) {
                eliminarEvento(event.id, event);
                bootstrap.Modal.getInstance(document.getElementById('modalEvento')).hide();
            }
        };

        toggleCampos();
        new bootstrap.Modal(document.getElementById('modalEvento')).show();
    }

    // Guardar evento nuevo
    function guardarEvento() {
        const form = document.getElementById('formEvento');
        const data = new FormData(form);

        fetch('{{ route("eventos.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: data
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalEvento')).hide();
                    window._calendar.refetchEvents();
                }
            })
            .catch(err => console.error(err));
    }

    // Actualizar evento desde modal
    function actualizarModal() {
        const id = document.getElementById('eventoId').value;
        const form = document.getElementById('formEvento');
        const data = new FormData(form);
        data.append('_method', 'PUT');

        fetch(`/eventos/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: data
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalEvento')).hide();
                    window._calendar.refetchEvents();
                }
            });
    }

    // Actualizar evento al arrastrar
    function actualizarEvento(event, revert) {
        const start = event.startStr;
        const end = event.endStr;

        fetch(`/eventos/${event.id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    fecha: start.substring(0, 10),
                    hora_inicio: start.includes('T') ? start.substring(11, 16) : null,
                    hora_fin: end && end.includes('T') ? end.substring(11, 16) : null,
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) revert();
            })
            .catch(() => revert());
    }

    // Eliminar evento
    function eliminarEvento(id, event) {
        fetch(`/eventos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window._calendar.refetchEvents();
                }
            });
    }

    function toggleCampos() {
        const tipo = document.getElementById('tipo').value;
        document.getElementById('camposOficio').classList.toggle('d-none', !['oficio', 'ambos'].includes(tipo));
        document.getElementById('camposRecibo').classList.toggle('d-none', !['recibo', 'ambos'].includes(tipo));
    }

    function toggleMonto() {
        const cobrado = document.getElementById('cobrado').value;
        document.getElementById('montoGroup').classList.toggle('d-none', cobrado !== 'si');
    }
</script>
@endsection