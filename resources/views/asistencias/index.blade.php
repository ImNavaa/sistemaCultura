@extends('layouts.app')
@section('title', 'Tablero de Asistencia')

@push('styles')
<style>
/* ── Variables de color ── */
:root {
    --color-presente: #10b981;
    --color-tarde:    #f59e0b;
    --color-ausente:  #ef4444;
    --color-inactivo: #94a3b8;
}

/* ── Header hero ── */
.asistencia-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    color: white;
    margin-bottom: 1.5rem;
}
.hero-reloj {
    font-size: 1.6rem;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    letter-spacing: .04em;
}
.hero-fecha { font-size: .85rem; opacity: .65; }

/* ── Stat cards ── */
.stat-card {
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .9rem;
    border: none;
}
.stat-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.stat-num  { font-size: 1.6rem; font-weight: 700; line-height: 1; }
.stat-label{ font-size: .75rem; color: var(--text-muted, #6c757d); margin-top: 2px; }

/* ── Tarjeta empleado ── */
.tarjeta-empleado {
    border-radius: 12px;
    border: 1px solid var(--border-color, #e9ecef);
    transition: box-shadow .2s, transform .15s, background .2s;
    overflow: hidden;
    background: var(--bg-card, #fff);
}
.tarjeta-empleado:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.tarjeta-empleado.no-laboral {
    background: var(--bg-card-alt, #f8f9fa);
    opacity: .75;
}
.tarjeta-top-bar {
    height: 4px;
    width: 100%;
}
.tarjeta-body { padding: 1rem 1rem .75rem; }

/* Avatar empleado */
.emp-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; font-weight: 700;
    flex-shrink: 0;
    color: white;
}

/* Dot presencia */
.presencia-dot {
    width: 9px; height: 9px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.dot-presente { background: var(--color-presente); box-shadow: 0 0 0 2px rgba(16,185,129,.25); animation: pulso 2s infinite; }
.dot-tarde    { background: var(--color-tarde);    box-shadow: 0 0 0 2px rgba(245,158,11,.25); animation: pulso 2s infinite; }
.dot-salio    { background: var(--color-inactivo); }
.dot-esperado { background: var(--color-ausente);  box-shadow: 0 0 0 2px rgba(239,68,68,.25); animation: pulso 2s infinite; }
.dot-vacio    { background: #dee2e6; }
@keyframes pulso {
    0%,100% { opacity:1; } 50% { opacity:.45; }
}

.chip {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .7rem;
    background: #f1f5f9;
    color: #475569;
    padding: 2px 8px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    white-space: nowrap;
}
.chip-presente    { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
.chip-tarde       { background: #fef3c7; color: #92400e; border-color: #fde68a; }
.chip-ausente     { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.chip-nolaboral   { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
.chip-saldo-horas { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
.chip-saldo-ec    { background: #ede9fe; color: #4c1d95; border-color: #c4b5fd; }

.horas-pill {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: .78rem;
    color: #334155;
}
.horas-pill .sep { color: #94a3b8; font-size: .65rem; }

.dias-row { display: flex; gap: 3px; flex-wrap: wrap; }
.dia-chip {
    font-size: .65rem;
    padding: 1px 5px;
    border-radius: 4px;
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
}
.dia-chip.hoy {
    background: #dbeafe;
    color: #1e40af;
    border-color: #93c5fd;
    font-weight: 700;
}

.detalle-asistencia {
    background: #f8fafc;
    border-radius: 8px;
    padding: .5rem .75rem;
    font-size: .78rem;
    color: #475569;
    margin-top: .5rem;
}

/* ── Stat card backgrounds (usadas en el HTML) ── */
.stat-bg-total    { background: #f1f5f9; }
.stat-bg-presente { background: #d1fae5; }
.stat-bg-falta    { background: #fee2e2; }
.stat-bg-sinreg   { background: #fef3c7; }
.stat-icon-total    { background: #e2e8f0; }
.stat-icon-presente { background: #a7f3d0; }
.stat-icon-falta    { background: #fca5a5; }
.stat-icon-sinreg   { background: #fde68a; }
.stat-num-presente  { color: #065f46; }
.stat-num-falta     { color: #991b1b; }
.stat-num-sinreg    { color: #92400e; }

/* ── Barra de eventos del día ── */
.eventos-bar        { background: #eff6ff; border-radius: 10px; border: 1px solid #bfdbfe; }
.eventos-bar-titulo { font-size: .8rem; font-weight: 600; color: #1e40af; }

/* ── Badge sin registro ── */
.badge-sinreg { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; font-size: .68rem; }

/* ── Caja de horario referencia en modal ── */
.horario-ref-box       { background: #eff6ff; border-radius: 8px; padding: .6rem 1rem; border: 1px solid #bfdbfe; }
.horario-ref-box small { color: #1e40af; }

/* ── Caja info guardia en modal ── */
.guardia-info-box { background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; font-size: .82rem; color: #475569; }

/* Colores para badge-purple, teal, indigo, orange */
.bg-purple { background-color: #6f42c1 !important; }
.bg-teal   { background-color: #20c997 !important; }
.bg-indigo { background-color: #6610f2 !important; }
.bg-orange { background-color: #fd7e14 !important; }

/* ── MODO OSCURO ── */
[data-theme="dark"] .tarjeta-empleado         { background: var(--bg-card); border-color: var(--border-color); }
[data-theme="dark"] .tarjeta-empleado.no-laboral { background: var(--bg-card-alt); }
[data-theme="dark"] .stat-label               { color: var(--text-muted); }
[data-theme="dark"] .chip                     { background: #2a2a45; color: #c5cae9; border-color: #3a3a55; }
[data-theme="dark"] .chip-presente            { background: #1a3a2e; color: #81c784;  border-color: #2d5a3d; }
[data-theme="dark"] .chip-tarde               { background: #3a2a10; color: #ffd54f;  border-color: #5a4015; }
[data-theme="dark"] .chip-ausente             { background: #3a1a1a; color: #ef9a9a;  border-color: #5a2525; }
[data-theme="dark"] .chip-nolaboral           { background: #2a2a45; color: #9fa8da;  border-color: #3a3a55; }
[data-theme="dark"] .chip-saldo-horas         { background: #1a3a2e; color: #81c784;  border-color: #2d5a3d; }
[data-theme="dark"] .chip-saldo-ec            { background: #2a1a45; color: #ce93d8;  border-color: #4a2a65; }
[data-theme="dark"] .horas-pill               { background: var(--bg-card-alt); border-color: var(--border-color); color: var(--text-body); }
[data-theme="dark"] .horas-pill .sep          { color: var(--text-muted); }
[data-theme="dark"] .dia-chip                 { background: #2a2a45; color: #9fa8da; border-color: #3a3a55; }
[data-theme="dark"] .dia-chip.hoy             { background: #1a2a4e; color: #90caf9; border-color: #2a4a7e; }
[data-theme="dark"] .detalle-asistencia       { background: var(--bg-card-alt); color: var(--text-body); }
[data-theme="dark"] .dot-vacio                { background: #3a3a55; }
[data-theme="dark"] .stat-bg-total            { background: #1e2535; }
[data-theme="dark"] .stat-bg-presente         { background: #0d2e20; }
[data-theme="dark"] .stat-bg-falta            { background: #2e1515; }
[data-theme="dark"] .stat-bg-sinreg           { background: #2e2510; }
[data-theme="dark"] .stat-icon-total          { background: #2a3045; }
[data-theme="dark"] .stat-icon-presente       { background: #1a4a30; }
[data-theme="dark"] .stat-icon-falta          { background: #4a2020; }
[data-theme="dark"] .stat-icon-sinreg         { background: #4a3a15; }
[data-theme="dark"] .stat-num-presente        { color: #81c784; }
[data-theme="dark"] .stat-num-falta           { color: #ef9a9a; }
[data-theme="dark"] .stat-num-sinreg          { color: #ffb74d; }
[data-theme="dark"] .eventos-bar              { background: #1a2035; border-color: #2a3555; }
[data-theme="dark"] .eventos-bar-titulo       { color: #90caf9; }
[data-theme="dark"] .badge-sinreg             { background: #2a2a45; color: #9fa8da; border-color: #3a3a55; }
[data-theme="dark"] .horario-ref-box          { background: #1a2035; border-color: #2a3555; }
[data-theme="dark"] .horario-ref-box small    { color: #90caf9; }
[data-theme="dark"] .guardia-info-box         { background: var(--bg-card-alt); border-color: var(--border-color); color: var(--text-body); }
</style>
@endpush

@section('content')

{{-- Hero header ── ──────────────────────────────────────── --}}
<div class="asistencia-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-person-check fs-4"></i>
                <h4 class="mb-0 fw-bold">Tablero de Asistencia</h4>
            </div>
            <div class="hero-fecha">
                <i class="bi bi-calendar3 me-1"></i>
                {{ $hoy->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </div>
        </div>
        <div class="text-end">
            <div class="hero-reloj" id="reloj">--:--:--</div>
            <button class="btn btn-sm mt-1"
                style="background:rgba(255,255,255,.15); color:white; border:1px solid rgba(255,255,255,.25);"
                onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Actualizar
            </button>
        </div>
    </div>
</div>

{{-- Stats ── ────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-bg-total">
            <div class="stat-icon stat-icon-total">
                <i class="bi bi-people text-secondary"></i>
            </div>
            <div>
                <div class="stat-num" style="color:var(--text-main)">{{ $stats['total'] }}</div>
                <div class="stat-label">Total empleados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-bg-presente">
            <div class="stat-icon stat-icon-presente">
                <i class="bi bi-person-check stat-num-presente"></i>
            </div>
            <div>
                <div class="stat-num stat-num-presente">{{ $stats['presentes'] }}</div>
                <div class="stat-label">Presentes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-bg-falta">
            <div class="stat-icon stat-icon-falta">
                <i class="bi bi-person-x stat-num-falta"></i>
            </div>
            <div>
                <div class="stat-num stat-num-falta">{{ $stats['faltas'] }}</div>
                <div class="stat-label">Faltas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-bg-sinreg">
            <div class="stat-icon stat-icon-sinreg">
                <i class="bi bi-question-circle stat-num-sinreg"></i>
            </div>
            <div>
                <div class="stat-num stat-num-sinreg">{{ $stats['sin_registro'] }}</div>
                <div class="stat-label">Sin registro</div>
            </div>
        </div>
    </div>
</div>

{{-- Eventos del día ──────────────────────────────────────── --}}
@if($eventosHoy->count() > 0)
<div class="d-flex align-items-center gap-2 flex-wrap mb-4 p-3 eventos-bar">
    <span class="eventos-bar-titulo">
        <i class="bi bi-calendar-event me-1"></i>Eventos hoy:
    </span>
    @foreach($eventosHoy as $ev)
    <span class="chip" style="background:#dbeafe; color:#1e40af; border-color:#93c5fd;">
        <i class="bi bi-star-fill" style="font-size:.55rem"></i>
        {{ $ev->nombre_evento }}
        @if($ev->hora_inicio) · {{ $ev->hora_inicio }} @endif
    </span>
    @endforeach
</div>
@endif

{{-- Tablero de tarjetas ──────────────────────────────────── --}}
@php
    $colores = ['primary'=>'#0d6efd','success'=>'#198754','warning'=>'#ffc107','danger'=>'#dc3545',
                'info'=>'#0dcaf0','secondary'=>'#6c757d','dark'=>'#212529',
                'purple'=>'#6f42c1','teal'=>'#20c997','indigo'=>'#6610f2','orange'=>'#fd7e14'];
    $avatarColores = ['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f97316','#eab308','#22c55e','#14b8a6','#06b6d4','#3b82f6'];
@endphp

<div class="row g-3">
    @foreach($empleados as $empleado)
    @php
        $asistencia   = $empleado->asistenciaActiva;
        $etiqueta     = $asistencia ? $asistencia->etiqueta() : null;
        $saldo        = $empleado->saldoTiempo;
        $diasEc       = $empleado->diasEconomicosAnio;
        $esMultiDia   = $asistencia && $asistencia->fecha_fin && $asistencia->fecha->toDateString() !== $hoy->toDateString();

        $diasLaborales = $empleado->dias_laborales ?? '';
        $diasArray     = array_map('trim', explode(',', $diasLaborales));
        $diasEsMap     = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
        $diaHoyNombre  = $diasEsMap[$hoy->dayOfWeek];
        $normalizar    = fn($s) => strtr(strtolower(trim($s)), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
        $esDiaLaboral  = count(array_filter($diasArray, fn($d) => $normalizar($d) === $normalizar($diaHoyNombre))) > 0;

        $colorBarra = $etiqueta ? ($colores[$etiqueta['color']] ?? '#94a3b8') : ($esDiaLaboral ? '#94a3b8' : '#e2e8f0');
        $avatarColor = $avatarColores[$empleado->id % count($avatarColores)];
        $inicial = strtoupper(substr($empleado->name, 0, 1));
    @endphp

    <div class="col-md-6 col-lg-4">
        <div class="tarjeta-empleado {{ !$esDiaLaboral ? 'no-laboral' : '' }} h-100"
            data-entrada="{{ $asistencia?->hora_entrada ?? '' }}"
            data-salida="{{ $asistencia?->hora_salida ?? '' }}"
            data-horario="{{ $empleado->horario ?? '' }}"
            data-diaslaborales="{{ $diasLaborales }}"
            data-diahoynombre="{{ $diaHoyNombre }}"
            data-eslaboral="{{ $esDiaLaboral ? '1' : '0' }}"
            data-estado="{{ $asistencia?->estado ?? '' }}">

            {{-- Barra de color superior --}}
            <div class="tarjeta-top-bar" style="background: {{ $colorBarra }}"></div>

            <div class="tarjeta-body">
                {{-- Fila principal: avatar + nombre + badge --}}
                <div class="d-flex align-items-start gap-2 mb-2">
                    <div class="emp-avatar" style="background: {{ $avatarColor }}">{{ $inicial }}</div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="fw-semibold text-truncate" style="font-size:.9rem;">{{ $empleado->name }}</div>
                        <div class="text-muted" style="font-size:.75rem;">{{ $empleado->cargo ?? '—' }}</div>
                    </div>
                    <div class="flex-shrink-0">
                        @if($asistencia)
                            <span class="badge bg-{{ $etiqueta['color'] }}" style="font-size:.68rem;">
                                {{ $etiqueta['icono'] }} {{ $etiqueta['label'] }}
                            </span>
                        @else
                            <span class="badge badge-sinreg">
                                Sin registro
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Indicador de presencia en tiempo real --}}
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="presencia-dot dot-vacio" data-dot></span>
                    <span class="chip" data-presencia-chip>
                        <span data-presencia-texto style="font-size:.68rem;">—</span>
                    </span>
                    <span class="chip chip-tarde d-none" data-tardanza>
                        <i class="bi bi-exclamation-triangle-fill" style="font-size:.6rem;"></i> Tardanza
                    </span>
                </div>

                {{-- Horario y días laborales --}}
                <div class="d-flex flex-wrap gap-2 mb-2 align-items-center">
                    @if($empleado->horario)
                    <div class="horas-pill">
                        <i class="bi bi-clock" style="color:#6366f1;font-size:.8rem;"></i>
                        {{ $empleado->horario }}
                    </div>
                    @endif
                    @if(!$esDiaLaboral)
                    <span class="chip chip-nolaboral">
                        <i class="bi bi-moon-stars-fill" style="font-size:.6rem;"></i> Día no laboral
                    </span>
                    @endif
                </div>

                @if($diasLaborales)
                <div class="dias-row mb-2">
                    @foreach($diasArray as $d)
                        @php $dn = substr(trim($d),0,3); $esHoy = $normalizar(trim($d)) === $normalizar($diaHoyNombre); @endphp
                        <span class="dia-chip {{ $esHoy ? 'hoy' : '' }}">{{ $dn }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Detalle del registro --}}
                @if($asistencia)
                <div class="detalle-asistencia">
                    @if($asistencia->hora_entrada || $asistencia->hora_salida)
                    <div class="d-flex align-items-center gap-2 mb-1">
                        @if($asistencia->hora_entrada)
                        <span><i class="bi bi-box-arrow-in-right text-success"></i> {{ $asistencia->hora_entrada }}</span>
                        @endif
                        @if($asistencia->hora_salida)
                        <span class="text-muted">→</span>
                        <span><i class="bi bi-box-arrow-right text-danger"></i> {{ $asistencia->hora_salida }}</span>
                        @endif
                        @if($asistencia->inmueble)
                        <span class="chip ms-1">
                            <i class="bi bi-building" style="font-size:.6rem;"></i> {{ $asistencia->inmueble }}
                        </span>
                        @endif
                    </div>
                    @endif
                    @if($asistencia->evento)
                    <div><i class="bi bi-calendar-event text-primary"></i> {{ $asistencia->evento->nombre_evento }}</div>
                    @endif
                    @if($esMultiDia && $asistencia->fecha_fin)
                    <div><i class="bi bi-calendar-range text-secondary"></i>
                        {{ $asistencia->fecha->format('d/m') }} — {{ $asistencia->fecha_fin->format('d/m/Y') }}
                    </div>
                    @elseif($asistencia->fecha_fin)
                    <div><i class="bi bi-calendar-range text-secondary"></i> Hasta {{ $asistencia->fecha_fin->format('d/m/Y') }}</div>
                    @endif
                    @if($asistencia->fecha_compensatorio)
                    <div><i class="bi bi-calendar-check text-success"></i> Comp. el {{ $asistencia->fecha_compensatorio->format('d/m/Y') }}</div>
                    @endif
                    @if($asistencia->folio_documento)
                    <div><i class="bi bi-file-text"></i> Folio: {{ $asistencia->folio_documento }}</div>
                    @endif
                    @if($asistencia->observaciones)
                    <div class="text-muted fst-italic"><i class="bi bi-chat-text"></i> {{ $asistencia->observaciones }}</div>
                    @endif
                </div>
                @endif

                {{-- Saldos --}}
                @if(($saldo && $saldo->saldo > 0) || ($diasEc && $diasEc->diasPendientes() > 0))
                <div class="d-flex gap-1 flex-wrap mt-2">
                    @if($saldo && $saldo->saldo > 0)
                    <span class="chip chip-saldo-horas">
                        <i class="bi bi-clock" style="font-size:.6rem;"></i>
                        {{ number_format($saldo->saldo, 1) }}h a favor
                    </span>
                    @endif
                    @if($diasEc && $diasEc->diasPendientes() > 0)
                    <span class="chip chip-saldo-ec">
                        <i class="bi bi-calendar3" style="font-size:.6rem;"></i>
                        {{ $diasEc->diasPendientes() }} días ec.
                    </span>
                    @endif
                </div>
                @endif

                {{-- Acciones --}}
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-sm flex-grow-1 fw-semibold"
                        style="background: {{ $colorBarra }}15; color: {{ $colorBarra }}; border: 1px solid {{ $colorBarra }}40;"
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
                        <i class="bi bi-pencil-square"></i>
                        {{ $asistencia ? 'Editar registro' : 'Registrar asistencia' }}
                    </button>
                    <a href="{{ route('asistencias.show', $empleado) }}"
                        class="btn btn-sm btn-outline-secondary" title="Historial" style="width:38px;">
                        <i class="bi bi-clock-history"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── MODAL ── ────────────────────────────────────────────── --}}
<div class="modal fade" id="modalAsistencia" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a1a2e,#0f3460); color:white; border-radius:.5rem .5rem 0 0;">
                <div>
                    <h5 class="modal-title mb-0" id="modalNombre">Registrar Asistencia</h5>
                    <small style="opacity:.65; font-size:.78rem;">
                        <i class="bi bi-calendar3"></i> {{ $hoy->locale('es')->isoFormat('dddd D [de] MMMM') }}
                    </small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formAsistencia">
                    @csrf
                    <input type="hidden" id="asistencia_user_id" name="user_id">
                    <input type="hidden" name="fecha" value="{{ $hoy->format('Y-m-d') }}">

                    {{-- Horario referencia --}}
                    <div class="d-none mb-3 horario-ref-box" id="horarioReferencia">
                        <small>
                            <i class="bi bi-clock-fill me-1"></i>
                            Horario registrado: <strong id="textoHorario"></strong>
                        </small>
                    </div>

                    {{-- Estado --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2">Estado <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach($etiquetas as $key => $info)
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="estado"
                                    id="estado_{{ $key }}" value="{{ $key }}"
                                    onchange="toggleCamposEstado('{{ $key }}')">
                                <label class="btn btn-outline-{{ $info['color'] }} w-100 py-2"
                                    for="estado_{{ $key }}" style="font-size:.78rem; border-radius:8px;">
                                    <div>{{ $info['icono'] }}</div>
                                    {{ $info['label'] }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Horas --}}
                    <div class="row g-3 mb-3" id="grupoHoras">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">
                                <i class="bi bi-box-arrow-in-right text-success"></i> Hora entrada
                            </label>
                            <input type="time" name="hora_entrada" id="hora_entrada" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">
                                <i class="bi bi-box-arrow-right text-danger"></i> Hora salida
                            </label>
                            <input type="time" name="hora_salida" id="hora_salida" class="form-control">
                        </div>
                    </div>

                    {{-- Evento --}}
                    <div class="mb-3 d-none" id="grupoEvento">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                            <i class="bi bi-calendar-event text-primary"></i> Evento del calendario
                        </label>
                        <select name="evento_id" id="evento_id" class="form-select">
                            <option value="">-- Sin vincular --</option>
                            @foreach($eventosHoy as $ev)
                            <option value="{{ $ev->id }}">
                                {{ $ev->nombre_evento }}{{ $ev->hora_inicio ? ' ('.$ev->hora_inicio.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fecha fin --}}
                    <div class="mb-3 d-none" id="grupoFechaFin">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                            <i class="bi bi-calendar-range"></i> Fecha de regreso
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control"
                            min="{{ $hoy->format('Y-m-d') }}">
                    </div>

                    {{-- Folio --}}
                    <div class="mb-3 d-none" id="grupoFolio">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                            <i class="bi bi-file-text"></i> Folio / Número de documento
                        </label>
                        <input type="text" name="folio_documento" id="folio_documento"
                            class="form-control" placeholder="Ej: IMSS-2024-001">
                    </div>

                    {{-- Compensatorio --}}
                    <div class="mb-3 d-none" id="grupoCompensatorio">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                            <i class="bi bi-calendar-check"></i> ¿Cuándo tomará el tiempo compensatorio?
                        </label>
                        <input type="date" name="fecha_compensatorio" id="fecha_compensatorio" class="form-control">
                    </div>

                    {{-- Día económico --}}
                    <div class="mb-3 d-none" id="grupoDiaEconomico">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                            <i class="bi bi-calendar3"></i> ¿Cuándo será el día económico?
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_dia_economico" class="form-control">
                        <div class="form-text">Deja en blanco si es hoy.</div>
                    </div>

                    {{-- Guardia aviso --}}
                    <div class="mb-3 d-none p-3 guardia-info-box" id="grupoGuardia">
                        <i class="bi bi-shield-fill-check me-1"></i>
                        Indica la hora de inicio y fin de guardia en los campos de entrada/salida.
                    </div>

                    {{-- Inmueble --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                            <i class="bi bi-building"></i> Inmueble
                        </label>
                        <select name="inmueble" id="inmueble" class="form-select">
                            <option value="">— Sin especificar —</option>
                            <option value="Ágora">Ágora</option>
                            <option value="Teatro Echeverría">Teatro Echeverría</option>
                        </select>
                    </div>

                    {{-- Observaciones --}}
                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                            <i class="bi bi-chat-text"></i> Observaciones
                        </label>
                        <textarea name="observaciones" id="observaciones"
                            class="form-control" rows="2"
                            placeholder="Notas adicionales..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" id="btnGuardar" onclick="guardarAsistencia()">
                    <i class="bi bi-save2"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// ── Reloj ──────────────────────────────────────────────────
function actualizarReloj() {
    const el = document.getElementById('reloj');
    if (el) el.textContent = new Date().toLocaleTimeString('es-MX');
}
setInterval(actualizarReloj, 1000);
actualizarReloj();

// ── Utilidades ────────────────────────────────────────────
function tiempoAMinutos(str) {
    if (!str) return null;
    const [h, m] = str.split(':').map(Number);
    return h * 60 + m;
}
function parsearHorario(horario) {
    if (!horario) return null;
    const m = horario.match(/(\d{1,2}:\d{2})\s*[-–]\s*(\d{1,2}:\d{2})/);
    return m ? { entrada: m[1], salida: m[2] } : null;
}

// ── Presencia en tiempo real ──────────────────────────────
function actualizarPresencias() {
    const ahora     = new Date();
    const minActual = ahora.getHours() * 60 + ahora.getMinutes();

    document.querySelectorAll('.tarjeta-empleado').forEach(card => {
        const entrada   = card.dataset.entrada;
        const salida    = card.dataset.salida;
        const horario   = card.dataset.horario;
        const estado    = card.dataset.estado;
        const esLaboral = card.dataset.eslaboral === '1';
        const dot       = card.querySelector('[data-dot]');
        const chipEl    = card.querySelector('[data-presencia-chip]');
        const textoEl   = card.querySelector('[data-presencia-texto]');
        const tardanza  = card.querySelector('[data-tardanza]');

        if (!esLaboral) {
            if (dot)    dot.className = 'presencia-dot dot-vacio';
            if (textoEl) textoEl.textContent = 'Día no laboral';
            if (tardanza) tardanza.classList.add('d-none');
            return;
        }

        const estadosPresencia = ['a_tiempo','tarde','cubriendo_evento','horas_extra','guardia','salida_temprana'];
        const horParsed     = parsearHorario(horario);
        const minHorEntrada = horParsed ? tiempoAMinutos(horParsed.entrada) : null;
        const minEntrada    = tiempoAMinutos(entrada);
        const minSalida     = tiempoAMinutos(salida);

        let dotClase = 'dot-vacio', texto = '—', esTarde = false;

        if (minEntrada !== null) {
            if (minSalida !== null && minActual >= minSalida) {
                dotClase = 'dot-salio';
                texto    = `Salió ${salida}`;
            } else {
                dotClase = 'dot-presente';
                texto    = `En trabajo desde ${entrada}`;
                if (minHorEntrada !== null && minEntrada > minHorEntrada + 10) esTarde = true;
            }
        } else if (estadosPresencia.includes(estado)) {
            dotClase = 'dot-presente';
            texto    = 'En trabajo';
        } else if (estado) {
            dotClase = 'dot-vacio';
            texto    = 'Ausencia registrada';
        } else if (minHorEntrada !== null) {
            if (minActual < minHorEntrada) {
                texto = `Esperado a las ${horParsed.entrada}`;
            } else if (minActual <= minHorEntrada + 30) {
                texto = `Sin registro (esperado ${horParsed.entrada})`;
            } else {
                dotClase = 'dot-esperado';
                texto    = `Sin registro desde ${horParsed.entrada}`;
                esTarde  = true;
            }
        } else {
            texto = 'Sin horario definido';
        }

        if (dot)    dot.className = `presencia-dot ${dotClase}`;
        if (textoEl) textoEl.textContent = texto;
        if (tardanza) tardanza.classList.toggle('d-none', !esTarde);
    });
}
setInterval(actualizarPresencias, 60000);
actualizarPresencias();

// ── Modal ─────────────────────────────────────────────────
function abrirDesdeBtn(btn) {
    const d = btn.dataset;
    document.getElementById('modalNombre').textContent = d.nombre;
    document.getElementById('asistencia_user_id').value = d.id;
    document.getElementById('hora_entrada').value = d.entrada || '';
    document.getElementById('hora_salida').value  = d.salida  || '';
    document.getElementById('evento_id').value    = d.evento  || '';
    document.getElementById('fecha_fin').value    = d.fechafin || '';
    document.getElementById('folio_documento').value   = d.folio  || '';
    document.getElementById('fecha_compensatorio').value = d.compensatorio || '';
    document.getElementById('observaciones').value = d.obs   || '';
    document.getElementById('inmueble').value      = d.inmueble || '';
    document.getElementById('btnGuardar').disabled  = false;
    document.getElementById('btnGuardar').innerHTML = '<i class="bi bi-save2"></i> Guardar';

    // Horario referencia + pre-llenado
    const refDiv    = document.getElementById('horarioReferencia');
    const horParsed = parsearHorario(d.horario || '');
    if (d.horario) {
        refDiv.classList.remove('d-none');
        document.getElementById('textoHorario').textContent = d.horario;
    } else {
        refDiv.classList.add('d-none');
    }
    if (horParsed) {
        if (!d.entrada) document.getElementById('hora_entrada').value = horParsed.entrada;
        if (!d.salida)  document.getElementById('hora_salida').value  = horParsed.salida;
    }

    document.querySelectorAll('input[name="estado"]').forEach(r => r.checked = false);
    if (d.estado) {
        const radio = document.getElementById('estado_' + d.estado);
        if (radio) { radio.checked = true; toggleCamposEstado(d.estado); }
    } else {
        toggleCamposEstado('');
    }

    new bootstrap.Modal(document.getElementById('modalAsistencia')).show();
}

function toggleCamposEstado(estado) {
    const grupos = {
        grupoEvento:        ['cubriendo_evento'],
        grupoFechaFin:      ['vacaciones', 'incapacidad'],
        grupoFolio:         ['incapacidad', 'cita_medica'],
        grupoCompensatorio: ['tiempo_compensatorio'],
        grupoDiaEconomico:  ['dia_economico'],
        grupoGuardia:       ['guardia'],
    };
    Object.entries(grupos).forEach(([id, estados]) => {
        document.getElementById(id)?.classList.toggle('d-none', !estados.includes(estado));
    });
    const sinHoras = ['vacaciones', 'falta_injustificada', 'falta_justificada'];
    document.getElementById('grupoHoras').classList.toggle('d-none', sinHoras.includes(estado));
}

function guardarAsistencia() {
    if (!document.querySelector('input[name="estado"]:checked')) {
        alert('⚠️ Selecciona un estado de asistencia.');
        return;
    }
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';

    fetch('{{ route("asistencias.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
        },
        body: new FormData(document.getElementById('formAsistencia'))
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalAsistencia')).hide();
            location.reload();
        } else {
            alert('❌ ' + (data.error || 'Error al guardar.'));
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save2"></i> Guardar';
        }
    })
    .catch(() => {
        alert('❌ Error de conexión.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save2"></i> Guardar';
    });
}
</script>
@endsection
