@extends('layouts.app')
@section('title', 'Tablero de Asistencia')
@section('content')

<style>
    .badge-purple {
        background-color: #6f42c1;
        color: #fff;
    }

    .badge-teal {
        background-color: #20c997;
        color: #fff;
    }

    .badge-indigo {
        background-color: #6610f2;
        color: #fff;
    }

    .badge-orange {
        background-color: #fd7e14;
        color: #fff;
    }

    .tarjeta-empleado {
        transition: transform 0.1s;
    }

    .tarjeta-empleado:hover {
        transform: translateY(-2px);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-person-check"></i> Tablero de Asistencia</h2>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted" style="font-size:0.85rem">
            <i class="bi bi-calendar3"></i> {{ $hoy->format('d/m/Y') }} —
            <i class="bi bi-clock"></i> <span id="reloj"></span>
        </span>
        <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise"></i> Actualizar
        </button>
    </div>
</div>

{{-- Estadísticas --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center border-0 bg-light">
            <div class="card-body py-2">
                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#d4edda">
            <div class="card-body py-2">
                <h3 class="mb-0 text-success">{{ $stats['presentes'] }}</h3>
                <small class="text-muted">Presentes</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#f8d7da">
            <div class="card-body py-2">
                <h3 class="mb-0 text-danger">{{ $stats['faltas'] }}</h3>
                <small class="text-muted">Faltas</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#fff3cd">
            <div class="card-body py-2">
                <h3 class="mb-0 text-warning">{{ $stats['sin_registro'] }}</h3>
                <small class="text-muted">Sin registro</small>
            </div>
        </div>
    </div>
</div>

{{-- Eventos del día --}}
@if($eventosHoy->count() > 0)
<div class="alert alert-primary mb-3 py-2">
    <strong><i class="bi bi-calendar-event"></i> Eventos hoy:</strong>
    @foreach($eventosHoy as $ev)
    <span class="badge bg-primary ms-1">
        {{ $ev->nombre_evento }}
        @if($ev->hora_inicio) · {{ $ev->hora_inicio }} @endif
    </span>
    @endforeach
</div>
@endif

{{-- Tablero --}}
<div class="row g-3">
    @foreach($empleados as $empleado)
    @php
    $asistencia = $empleado->asistenciaHoy;
    $etiqueta = $asistencia ? $asistencia->etiqueta() : null;
    $saldo = $empleado->saldoTiempo;
    $diasEc = $empleado->diasEconomicosAnio;
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100 tarjeta-empleado"
            style="border-left: 4px solid {{ $etiqueta ? "var(--bs-{$etiqueta['color']})" : '#adb5bd' }}">
            <div class="card-body pb-2">

                {{-- Encabezado --}}
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $empleado->name }}</h6>
                        <small class="text-muted">{{ $empleado->cargo ?? '—' }}</small>
                    </div>
                    @if($asistencia)
                    <span class="badge badge-{{ $etiqueta['color'] }} bg-{{ $etiqueta['color'] }}">
                        {{ $etiqueta['icono'] }} {{ $etiqueta['label'] }}
                    </span>
                    @else
                    <span class="badge bg-light text-dark border">Sin registro</span>
                    @endif
                </div>

                {{-- Detalle asistencia --}}
                @if($asistencia)
                <div class="small text-muted mb-1">
                    @if($asistencia->hora_entrada)
                    <i class="bi bi-box-arrow-in-right text-success"></i> {{ $asistencia->hora_entrada }}
                    @endif
                    @if($asistencia->hora_salida)
                    → <i class="bi bi-box-arrow-right text-danger"></i> {{ $asistencia->hora_salida }}
                    @endif
                    @if($asistencia->inmueble)
                    · <i class="bi bi-building"></i> {{ $asistencia->inmueble }}
                    @endif
                    @if($asistencia->evento)
                    <div><i class="bi bi-calendar-event text-primary"></i> {{ $asistencia->evento->nombre_evento }}</div>
                    @endif
                    @if($asistencia->fecha_fin)
                    <div><i class="bi bi-calendar-range"></i> Hasta: {{ $asistencia->fecha_fin->format('d/m/Y') }}</div>
                    @endif
                    @if($asistencia->fecha_compensatorio)
                    <div><i class="bi bi-calendar-check"></i> Comp. el: {{ $asistencia->fecha_compensatorio->format('d/m/Y') }}</div>
                    @endif
                    @if($asistencia->folio_documento)
                    <div><i class="bi bi-file-text"></i> Folio: {{ $asistencia->folio_documento }}</div>
                    @endif
                    @if($asistencia->observaciones)
                    <div><i class="bi bi-chat-text"></i> {{ $asistencia->observaciones }}</div>
                    @endif
                </div>
                @endif

                {{-- Saldos --}}
                <div class="d-flex gap-1 flex-wrap mb-2">
                    @if($saldo && $saldo->saldo > 0)
                    <span class="badge bg-success" title="Horas a favor">
                        <i class="bi bi-clock"></i> {{ number_format($saldo->saldo, 1) }}h favor
                    </span>
                    @endif
                    @if($diasEc && $diasEc->diasPendientes() > 0)
                    <span class="badge bg-indigo text-white" title="Días económicos pendientes">
                        <i class="bi bi-calendar3"></i> {{ $diasEc->diasPendientes() }} días ec.
                    </span>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary flex-grow-1"
                        data-id="{{ $empleado->id }}"
                        data-nombre="{{ $empleado->name }}"
                        data-horario="{{ $empleado->horario ?? '' }}"
                        data-estado="{{ $asistencia?->estado ?? '' }}"
                        data-entrada="{{ $asistencia?->hora_entrada ?? '' }}"
                        data-salida="{{ $asistencia?->hora_salida ?? '' }}"
                        data-evento="{{ $asistencia?->evento_id ?? '' }}"
                        data-fechafin="{{ $asistencia?->fecha_fin?->format('Y-m-d') ?? '' }}"
                        data-folio="{{ $asistencia?->folio_documento ?? '' }}"
                        data-compensatorio="{{ $asistencia?->fecha_compensatorio?->format('Y-m-d') ?? '' }}"
                        data-inmueble="{{ $asistencia?->inmueble ?? '' }}"
                        data-obs="{{ $asistencia?->observaciones ?? '' }}"
                        onclick="abrirDesdeBtn(this)">
                        <i class="bi bi-pencil"></i> {{ $asistencia ? 'Editar' : 'Registrar' }}
                    </button>
                    <a href="{{ route('asistencias.show', $empleado) }}"
                        class="btn btn-sm btn-outline-secondary" title="Historial">
                        <i class="bi bi-clock-history"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- MODAL --}}
<div class="modal fade" id="modalAsistencia" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNombre">Registrar Asistencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAsistencia">
                    @csrf
                    <input type="hidden" id="asistencia_user_id" name="user_id">
                    <input type="hidden" name="fecha" value="{{ $hoy->format('Y-m-d') }}">

                    {{-- Referencia horario --}}
                    <div class="alert alert-light border mb-3 py-2 d-none" id="horarioReferencia">
                        <small><i class="bi bi-clock"></i> Horario: <strong id="textoHorario"></strong></small>
                    </div>

                    {{-- Estado --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach($etiquetas as $key => $info)
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="estado"
                                    id="estado_{{ $key }}" value="{{ $key }}"
                                    onchange="toggleCamposEstado('{{ $key }}')">
                                <label class="btn btn-outline-{{ $info['color'] }} w-100 py-1"
                                    for="estado_{{ $key }}" style="font-size:0.8rem">
                                    {{ $info['icono'] }} {{ $info['label'] }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Horas entrada/salida --}}
                    <div class="row g-2 mb-3" id="grupoHoras">
                        <div class="col-6">
                            <label class="form-label">Hora entrada</label>
                            <input type="time" name="hora_entrada" id="hora_entrada" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Hora salida</label>
                            <input type="time" name="hora_salida" id="hora_salida" class="form-control">
                        </div>
                    </div>

                    {{-- Vincular evento del calendario --}}
                    <div class="mb-3 d-none" id="grupoEvento">
                        <label class="form-label">
                            <i class="bi bi-calendar-event text-primary"></i> Evento del calendario
                        </label>
                        <select name="evento_id" id="evento_id" class="form-select">
                            <option value="">-- Seleccionar evento --</option>
                            @foreach($eventosHoy as $ev)
                            <option value="{{ $ev->id }}">
                                {{ $ev->nombre_evento }}
                                @if($ev->hora_inicio) ({{ $ev->hora_inicio }}) @endif
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">Eventos programados para hoy</div>
                    </div>

                    {{-- Rango de fechas (vacaciones/incapacidad) --}}
                    <div class="mb-3 d-none" id="grupoFechaFin">
                        <label class="form-label">
                            <i class="bi bi-calendar-range"></i> Fecha de regreso
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control"
                            min="{{ $hoy->format('Y-m-d') }}">
                    </div>

                    {{-- Folio documento (incapacidad/cita) --}}
                    <div class="mb-3 d-none" id="grupoFolio">
                        <label class="form-label">
                            <i class="bi bi-file-text"></i> Folio / Número de documento
                        </label>
                        <input type="text" name="folio_documento" id="folio_documento"
                            class="form-control" placeholder="Ej: IMSS-2024-001">
                    </div>

                    {{-- Fecha compensatorio --}}
                    <div class="mb-3 d-none" id="grupoCompensatorio">
                        <label class="form-label">
                            <i class="bi bi-calendar-check"></i> ¿Cuándo tomará el tiempo compensatorio?
                        </label>
                        <input type="date" name="fecha_compensatorio" id="fecha_compensatorio"
                            class="form-control">
                    </div>

                    {{-- Inmueble --}}
                    <div class="mb-3">
                        <label class="form-label">Inmueble / Área</label>
                        <input type="text" name="inmueble" id="inmueble" class="form-control"
                            placeholder="Ej: Teatro, Oficina, etc.">
                    </div>

                    {{-- Observaciones --}}
                    <div class="mb-0">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="observaciones"
                            class="form-control" rows="2"
                            placeholder="Notas adicionales"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardar" onclick="guardarAsistencia()">
                    <i class="bi bi-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function actualizarReloj() {
        const el = document.getElementById('reloj');
        if (el) el.textContent = new Date().toLocaleTimeString('es-MX');
    }
    setInterval(actualizarReloj, 1000);
    actualizarReloj();

    function abrirDesdeBtn(btn) {
        const d = btn.dataset;
        document.getElementById('modalNombre').textContent = d.nombre;
        document.getElementById('asistencia_user_id').value = d.id;
        document.getElementById('hora_entrada').value = d.entrada || '';
        document.getElementById('hora_salida').value = d.salida || '';
        document.getElementById('evento_id').value = d.evento || '';
        document.getElementById('fecha_fin').value = d.fechafin || '';
        document.getElementById('folio_documento').value = d.folio || '';
        document.getElementById('fecha_compensatorio').value = d.compensatorio || '';
        document.getElementById('inmueble').value = d.inmueble || '';
        document.getElementById('observaciones').value = d.obs || '';
        document.getElementById('btnGuardar').disabled = false;
        document.getElementById('btnGuardar').innerHTML = '<i class="bi bi-save"></i> Guardar';

        // Horario referencia
        const refDiv = document.getElementById('horarioReferencia');
        if (d.horario) {
            refDiv.classList.remove('d-none');
            document.getElementById('textoHorario').textContent = d.horario;
        } else {
            refDiv.classList.add('d-none');
        }

        // Seleccionar estado
        document.querySelectorAll('input[name="estado"]').forEach(r => r.checked = false);
        if (d.estado) {
            const radio = document.getElementById('estado_' + d.estado);
            if (radio) {
                radio.checked = true;
                toggleCamposEstado(d.estado);
            }
        } else {
            toggleCamposEstado('');
        }

        new bootstrap.Modal(document.getElementById('modalAsistencia')).show();
    }

    // Muestra/oculta campos según el estado seleccionado
    function toggleCamposEstado(estado) {
        const grupos = {
            grupoEvento: ['cubriendo_evento'],
            grupoFechaFin: ['vacaciones', 'incapacidad'],
            grupoFolio: ['incapacidad', 'cita_medica'],
            grupoCompensatorio: ['tiempo_compensatorio'],
        };

        Object.entries(grupos).forEach(([grupoId, estados]) => {
            const el = document.getElementById(grupoId);
            if (!el) return;
            if (estados.includes(estado)) {
                el.classList.remove('d-none');
            } else {
                el.classList.add('d-none');
            }
        });

        // Ocultar horas para vacaciones y día económico
        const grupoHoras = document.getElementById('grupoHoras');
        if (['vacaciones', 'dia_economico', 'falta_injustificada', 'falta_justificada'].includes(estado)) {
            grupoHoras.classList.add('d-none');
        } else {
            grupoHoras.classList.remove('d-none');
        }
    }

    function guardarAsistencia() {
        const estado = document.querySelector('input[name="estado"]:checked');
        if (!estado) {
            alert('⚠️ Selecciona un estado de asistencia.');
            return;
        }

        const btn = document.getElementById('btnGuardar');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';

        const data = new FormData(document.getElementById('formAsistencia'));

        fetch('{{ route("asistencias.store") }}', {
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
                    bootstrap.Modal.getInstance(document.getElementById('modalAsistencia')).hide();
                    location.reload();
                } else {
                    alert('❌ ' + (data.error || 'Error al guardar.'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-save"></i> Guardar';
                }
            })
            .catch(function() {
                alert('❌ Error de conexión.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-save"></i> Guardar';
            });
    }
</script>
@endsection