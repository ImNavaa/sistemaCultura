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
                            <select id="autoriza" name="autoriza" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                <option value="Presidente">Presidente</option>
                                <option value="Secretario">Secretario</option>
                                <option value="Finanzas">Finanzas</option>
                                <option value="Dirección de Cultura">Dirección de Cultura</option>
                                <option value="Se desconoce">Se desconoce</option>
                            </select>
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
                            <input type="text" id="nombre_evento" name="nombre_evento"
                                class="form-control" placeholder="Escribe el nombre del evento" required>
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

            // ✅ POKA-YOKE: Bloquear fechas pasadas al hacer clic
            dateClick: function(info) {
                /*
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                const clicked = new Date(info.dateStr);

                if (clicked < hoy) {
                    mostrarAlerta('⚠️ No puedes crear eventos en fechas pasadas.', 'warning');
                    return;
                }
                */
                abrirModal(info.dateStr);
            },

            eventClick: function(info) {
                abrirModalEditar(info.event);
            },

            eventDrop: function(info) {
                verificarConflicto(info.event, function(conflicto) {
                    if (conflicto) {
                        if (!confirm(`⚠️ Ya existe un evento en esa fecha y hora: "${conflicto}". ¿Deseas continuar de todas formas?`)) {
                            info.revert();
                            return;
                        }
                    }
                    actualizarEvento(info.event, info.revert);
                });
            },

            eventResize: function(info) {
                actualizarEvento(info.event, info.revert);
            },

            eventMouseEnter: function(info) {
                const p = info.event.extendedProps;
                info.el.setAttribute('title',
                    `Organizador: ${p.organizador}\nAutoriza: ${p.autoriza ?? '—'}\nTipo: ${p.tipo}`
                );
            },

            datesSet: function(info) {
                const fecha = info.view.currentStart;
                document.getElementById('selectMes').value = fecha.getMonth();
                document.getElementById('selectAnio').value = fecha.getFullYear();
            }
        });

        calendar.render();
        window._calendar = calendar;

        // Inicializa selectores
        const hoy = new Date();
        document.getElementById('selectMes').value = hoy.getMonth();
        document.getElementById('selectAnio').value = hoy.getFullYear();

        // ✅ POKA-YOKE: Capitalizar automáticamente mientras escribe
        // ✅ POKA-YOKE: Capitalizar automáticamente mientras escribe
        ['organizador'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    const pos = this.selectionStart;
                    const val = this.value.replace(/\b\w/g, l => l.toUpperCase());
                    if (val !== this.value) {
                        this.value = val;
                        try {
                            this.setSelectionRange(pos, pos);
                        } catch (e) {
                            // ignorar si el navegador no lo soporta
                        }
                    }
                });
            }
        });

        // ✅ POKA-YOKE: Validar hora fin > hora inicio en tiempo real
        document.getElementById('hora_fin').addEventListener('change', validarHoras);
        document.getElementById('hora_inicio').addEventListener('change', validarHoras);
    });

    // ✅ Capitalizar primera letra de cada palabra
    function capitalizarTexto(texto) {
        return texto.replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
    }

    // ✅ Validar que hora fin sea mayor que hora inicio
    function validarHoras() {
        const inicio = document.getElementById('hora_inicio').value;
        const fin = document.getElementById('hora_fin').value;

        if (inicio && fin && fin <= inicio) {
            mostrarAlerta('⚠️ La hora de fin debe ser mayor que la hora de inicio.', 'danger');
            document.getElementById('hora_fin').value = '';
            document.getElementById('hora_fin').classList.add('is-invalid');
        } else {
            document.getElementById('hora_fin').classList.remove('is-invalid');
        }
    }

    // ✅ Verificar conflicto de evento en misma fecha y hora
    function verificarConflicto(event, callback) {
        const eventos = window._calendar.getEvents();
        const nuevaFecha = event.startStr.substring(0, 10);
        const nuevaHoraI = event.startStr.includes('T') ? event.startStr.substring(11, 16) : null;

        const conflicto = eventos.find(function(e) {
            if (e.id === event.id) return false;
            const eFecha = e.startStr.substring(0, 10);
            const eHoraI = e.startStr.includes('T') ? e.startStr.substring(11, 16) : null;
            return eFecha === nuevaFecha && nuevaHoraI && eHoraI && eHoraI === nuevaHoraI;
        });

        callback(conflicto ? conflicto.title : null);
    }

    // ✅ Mostrar alerta temporal en pantalla
    function mostrarAlerta(mensaje, tipo = 'warning') {
        const div = document.createElement('div');
        div.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
        div.style.cssText = 'top:20px; right:20px; z-index:9999; min-width:320px; box-shadow: 0 4px 12px rgba(0,0,0,0.15)';
        div.innerHTML = `${mensaje} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(div);
        setTimeout(function() {
            div.remove();
        }, 4000);
    }

    // Navegar a mes/año
    function navegarFecha() {
        const mes = parseInt(document.getElementById('selectMes').value);
        const anio = parseInt(document.getElementById('selectAnio').value);
        window._calendar.gotoDate(new Date(anio, mes, 1));
    }

    // Abrir modal CREAR
    function abrirModal(fecha = '') {
        document.getElementById('modalTitulo').textContent = 'Nuevo Evento';
        document.getElementById('eventoId').value = '';
        document.getElementById('fecha').value = fecha;
        document.getElementById('nombre_evento').value = '';
        document.getElementById('organizador').value = '';
        document.getElementById('autoriza').value = '';
        document.getElementById('hora_inicio').value = '';
        document.getElementById('hora_fin').value = '';
        document.getElementById('tipo').value = 'ninguno';
        document.getElementById('numero_recibo').value = '';
        document.getElementById('btnGuardar').textContent = 'Guardar Evento';
        document.getElementById('btnGuardar').onclick = guardarEvento;
        document.getElementById('btnGuardar').disabled = false;
        document.getElementById('btnEliminar').classList.add('d-none');
        toggleCampos();
        new bootstrap.Modal(document.getElementById('modalEvento')).show();
    }

    // Abrir modal EDITAR
    function abrirModalEditar(event) {
        const p = event.extendedProps;

        document.getElementById('modalTitulo').textContent = 'Editar Evento';
        document.getElementById('eventoId').value = event.id;
        document.getElementById('fecha').value = event.startStr.substring(0, 10);
        document.getElementById('nombre_evento').value = event.title;
        document.getElementById('organizador').value = p.organizador ?? '';
        document.getElementById('autoriza').value = p.autoriza ?? '';
        document.getElementById('hora_inicio').value = p.hora_inicio ?? '';
        document.getElementById('hora_fin').value = p.hora_fin ?? '';
        document.getElementById('tipo').value = p.tipo ?? 'ninguno';
        document.getElementById('numero_recibo').value = p.numero_recibo ?? '';
        document.getElementById('btnGuardar').textContent = 'Actualizar Evento';
        document.getElementById('btnGuardar').onclick = actualizarModal;
        document.getElementById('btnGuardar').disabled = false;

        // ✅ POKA-YOKE: Botón eliminar con confirmación
        const btnEliminar = document.getElementById('btnEliminar');
        btnEliminar.classList.remove('d-none');
        btnEliminar.onclick = function() {
            const nombre = event.title;
            const fecha = event.startStr.substring(0, 10);
            if (confirm(`⚠️ ¿Estás seguro de eliminar el evento?\n\n"${nombre}" del ${fecha}\n\nEsta acción no se puede deshacer.`)) {
                eliminarEvento(event.id);
                bootstrap.Modal.getInstance(document.getElementById('modalEvento')).hide();
            }
        };

        toggleCampos();
        new bootstrap.Modal(document.getElementById('modalEvento')).show();
    }

    // ✅ POKA-YOKE: Validar formulario antes de guardar
    function validarFormulario() {
        const nombre = document.getElementById('nombre_evento').value.trim();
        const organizador = document.getElementById('organizador').value.trim();
        const fecha = document.getElementById('fecha').value;
        const horaInicio = document.getElementById('hora_inicio').value;
        const horaFin = document.getElementById('hora_fin').value;

        if (!nombre) {
            mostrarAlerta('⚠️ El nombre del evento es obligatorio.', 'danger');
            document.getElementById('nombre_evento').focus();
            return false;
        }

        if (!organizador) {
            mostrarAlerta('⚠️ El organizador es obligatorio.', 'danger');
            document.getElementById('organizador').focus();
            return false;
        }

        if (!fecha) {
            mostrarAlerta('⚠️ La fecha es obligatoria.', 'danger');
            document.getElementById('fecha').focus();
            return false;
        }

        if (horaInicio && horaFin && horaFin <= horaInicio) {
            mostrarAlerta('⚠️ La hora de fin debe ser mayor que la hora de inicio.', 'danger');
            document.getElementById('hora_fin').focus();
            return false;
        }

        return true;
    }

    // ✅ POKA-YOKE: Advertir si ya existe evento en misma fecha y hora al guardar
    function verificarConflictoAlGuardar(callback) {
        const fecha = document.getElementById('fecha').value;
        const horaInicio = document.getElementById('hora_inicio').value;
        const eventoId = document.getElementById('eventoId').value;

        if (!horaInicio) {
            callback(false);
            return;
        }

        const eventos = window._calendar.getEvents();
        const conflicto = eventos.find(function(e) {
            if (e.id == eventoId) return false;
            const eFecha = e.startStr.substring(0, 10);
            const eHora = e.extendedProps.hora_inicio;
            return eFecha === fecha && eHora === horaInicio;
        });

        if (conflicto) {
            if (!confirm(`⚠️ Ya existe el evento "${conflicto.title}" en esa fecha y hora.\n\n¿Deseas guardarlo de todas formas?`)) {
                callback(true);
                return;
            }
        }
        callback(false);
    }

    // Guardar evento nuevo
    function guardarEvento() {
        if (!validarFormulario()) return;

        verificarConflictoAlGuardar(function(cancelado) {
            if (cancelado) return;

            // ✅ POKA-YOKE: Prevenir doble clic
            const btn = document.getElementById('btnGuardar');
            btn.disabled = true;
            btn.textContent = 'Guardando...';

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
                        mostrarAlerta('✅ Evento guardado correctamente.', 'success');
                    } else {
                        mostrarAlerta('❌ Error al guardar el evento.', 'danger');
                        btn.disabled = false;
                        btn.textContent = 'Guardar Evento';
                    }
                })
                .catch(function() {
                    mostrarAlerta('❌ Error de conexión.', 'danger');
                    btn.disabled = false;
                    btn.textContent = 'Guardar Evento';
                });
        });
    }

    // Actualizar evento desde modal
    function actualizarModal() {
        if (!validarFormulario()) return;

        verificarConflictoAlGuardar(function(cancelado) {
            if (cancelado) return;

            // ✅ POKA-YOKE: Prevenir doble clic
            const btn = document.getElementById('btnGuardar');
            btn.disabled = true;
            btn.textContent = 'Actualizando...';

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
                        mostrarAlerta('✅ Evento actualizado correctamente.', 'success');
                    } else {
                        mostrarAlerta('❌ Error al actualizar.', 'danger');
                        btn.disabled = false;
                        btn.textContent = 'Actualizar Evento';
                    }
                })
                .catch(function() {
                    mostrarAlerta('❌ Error de conexión.', 'danger');
                    btn.disabled = false;
                    btn.textContent = 'Actualizar Evento';
                });
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
                if (data.success) {
                    mostrarAlerta('✅ Evento movido correctamente.', 'success');
                } else {
                    revert();
                }
            })
            .catch(function() {
                revert();
            });
    }

    // Eliminar evento
    function eliminarEvento(id) {
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
                    mostrarAlerta('✅ Evento eliminado correctamente.', 'success');
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

{{-- ✅ POKA-YOKE: Autocompletar nombres de eventos --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route("eventos.get") }}')
            .then(res => res.json())
            .then(eventos => {
                const nombres = [...new Set(eventos.map(e => e.title))];
                const datalist = document.createElement('datalist');
                datalist.id = 'sugerenciasEventos';
                nombres.forEach(function(nombre) {
                    const opt = document.createElement('option');
                    opt.value = nombre;
                    datalist.appendChild(opt);
                });
                document.body.appendChild(datalist);
            });
    });
</script>
@endsection