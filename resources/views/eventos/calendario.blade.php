@extends('layouts.app')

@section('title', 'Calendario de Eventos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar3"></i> Calendario de Eventos</h2>
    <button class="btn btn-primary" onclick="abrirModal()">
        <i class="bi bi-plus-circle"></i> Nuevo Evento
    </button>
</div>

<div class="d-flex gap-3 mb-3 flex-wrap">
    <span><span style="background:#3a7bd5" class="badge">&nbsp;</span> Oficio</span>
    <span><span style="background:#7b3ad5" class="badge">&nbsp;</span> Recibo</span>
    <span><span style="background:#e8c547" class="badge">&nbsp;</span> Oficio + Recibo</span>
    <span><span style="background:#555" class="badge">&nbsp;</span> Sin documento</span>
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
                <h5 class="modal-title">Nuevo Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEvento">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" id="fecha" name="fecha" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Organizador <span class="text-danger">*</span></label>
                            <input type="text" id="organizador" name="organizador" class="form-control" placeholder="Nombre del organizador" required>
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
                            <input type="text" id="nombre_evento" name="nombre_evento" class="form-control" placeholder="Nombre del evento" required>
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
                                    <h6 class="text-primary mb-3"><i class="bi bi-file-earmark-text"></i> Datos del Oficio</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Número de Oficio</label>
                                            <input type="text" id="numero_oficio" name="numero_oficio" class="form-control" placeholder="OF-2024-001">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">¿Se cobró?</label>
                                            <select id="cobrado" name="cobrado" class="form-select" onchange="toggleMonto()">
                                                <option value="no">No</option>
                                                <option value="si">Sí</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-none" id="montoGroup">
                                            <label class="form-label">Monto ($)</label>
                                            <input type="number" id="monto_cobrado" name="monto_cobrado" class="form-control" placeholder="0.00" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos Recibo -->
                        <div id="camposRecibo" class="col-12 d-none">
                            <div class="card bg-light" style="border-color:#7b3ad5!important; border:1px solid">
                                <div class="card-body">
                                    <h6 style="color:#7b3ad5" class="mb-3"><i class="bi bi-receipt"></i> Datos del Recibo</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Importe ($)</label>
                                            <input type="number" id="importe" name="importe" class="form-control" placeholder="0.00" step="0.01" min="0">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Concepto</label>
                                            <input type="text" id="concepto" name="concepto" class="form-control" placeholder="Concepto del recibo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEvento()">
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
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },
        events: '{{ route("eventos.get") }}',
        dateClick: function(info) {
            abrirModal(info.dateStr);
        },
        eventClick: function(info) {
            if (confirm(`¿Eliminar el evento "${info.event.title}"?`)) {
                eliminarEvento(info.event.id, info.event);
            }
        },
        eventMouseEnter: function(info) {
            const p = info.event.extendedProps;
            info.el.setAttribute('title',
                `Organizador: ${p.organizador}\nTipo: ${p.tipo}`
            );
        }
    });

    calendar.render();
    window._calendar = calendar;
});

function abrirModal(fecha = '') {
    document.getElementById('fecha').value = fecha;
    document.getElementById('nombre_evento').value = '';
    document.getElementById('organizador').value = '';
    document.getElementById('hora_inicio').value = '';
    document.getElementById('hora_fin').value = '';
    document.getElementById('tipo').value = 'ninguno';
    toggleCampos();
    new bootstrap.Modal(document.getElementById('modalEvento')).show();
}

function toggleCampos() {
    const tipo = document.getElementById('tipo').value;
    document.getElementById('camposOficio').classList.toggle('d-none', !['oficio','ambos'].includes(tipo));
    document.getElementById('camposRecibo').classList.toggle('d-none', !['recibo','ambos'].includes(tipo));
}

function toggleMonto() {
    const cobrado = document.getElementById('cobrado').value;
    document.getElementById('montoGroup').classList.toggle('d-none', cobrado !== 'si');
}

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
            event.remove();
        }
    });
}
</script>
@endsection