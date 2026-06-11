@extends('layouts.app')
@section('title', 'Dashboard RH')
@section('content')

<style>
/* ── RH Dashboard ────────────────────────────── */
.rh-stat { border-radius:14px; padding:18px 20px; display:flex; align-items:center; gap:16px; background:var(--bg-card); border:1px solid var(--border-color); }
.rh-stat-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; }
.rh-stat-icon.blue   { background:#e3f2fd; color:#1565c0; }
.rh-stat-icon.green  { background:#e8f5e9; color:#2e7d32; }
.rh-stat-icon.amber  { background:#fff8e1; color:#f57f17; }
.rh-stat-icon.rose   { background:#fce4ec; color:#c62828; }
.rh-stat-icon.indigo { background:#e8eaf6; color:#283593; }
.rh-stat-icon.teal   { background:#e0f2f1; color:#00695c; }
.rh-stat-val  { font-size:1.7rem; font-weight:700; line-height:1; }
.rh-stat-lbl  { font-size:.78rem; color:var(--text-muted); margin-top:2px; }

/* Recinto badges */
.recinto-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:600; }
.recinto-Teatro       { background:#e3f2fd; color:#1565c0; }
.recinto-Agora        { background:#ede9fe; color:#5b21b6; }
.recinto-Oficinas     { background:#e8f5e9; color:#2e7d32; }
.recinto-Biblioteca   { background:#fff8e1; color:#b45309; }
.recinto-Museo        { background:#fce4ec; color:#9f1239; }
.recinto-Otro         { background:#f3f4f6; color:#374151; }
.recinto-Sin          { background:#f3f4f6; color:#9ca3af; }

/* Cumpleaños countdown */
.cumple-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:12px; padding:12px 14px; display:flex; align-items:center; gap:12px; }
.cumple-avatar { width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1rem; flex-shrink:0; }
.cumple-hoy   { border:2px solid #f59e0b; background:#fff8e1; color:#92400e; }
.cumple-sem   { border:2px solid #6366f1; background:#e0e7ff; color:#3730a3; }
.cumple-mes   { border:2px solid var(--border-color); background:var(--bg-card-alt); color:var(--text-main); }
.dias-chip    { min-width:36px; text-align:center; border-radius:20px; padding:2px 8px; font-size:.75rem; font-weight:700; }
.dias-chip.hoy  { background:#fef3c7; color:#92400e; }
.dias-chip.near { background:#e0e7ff; color:#3730a3; }
.dias-chip.far  { background:var(--bg-card-alt); color:var(--text-muted); }

/* Employee mini-card */
.emp-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:14px; padding:16px; transition:.15s; }
.emp-card:hover { border-color:var(--accent,#3a7bd5); transform:translateY(-2px); box-shadow:0 4px 16px rgba(0,0,0,.08); }
.emp-avatar { width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; flex-shrink:0; }
.emp-mini-stat { flex:1; text-align:center; padding:6px 4px; border-radius:8px; background:var(--bg-card-alt); }
.emp-mini-val  { font-size:1rem; font-weight:700; line-height:1; }
.emp-mini-lbl  { font-size:.65rem; color:var(--text-muted); margin-top:2px; }

/* Sección alerts */
.alert-rh { border-radius:10px; padding:10px 14px; font-size:.85rem; display:flex; align-items:center; gap:10px; }
.alert-rh.cumple-hoy-alert  { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
.alert-rh.cumple-near-alert { background:#e0e7ff; color:#3730a3; border:1px solid #c7d2fe; }
</style>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon" style="background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <h2>Recursos Humanos</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Dashboard RH</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('vacaciones.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-umbrella me-1"></i>Vacaciones
        </a>
        <a href="{{ route('dias-pendientes.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-calendar-check me-1"></i>Días Pendientes
        </a>
        <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-person-lines-fill me-1"></i>Empleados
        </a>
    </div>
</div>

{{-- ── Stats principales ───────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="rh-stat">
            <div class="rh-stat-icon blue"><i class="bi bi-people"></i></div>
            <div>
                <div class="rh-stat-val">{{ $totalPersonal }}</div>
                <div class="rh-stat-lbl">Total personal</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="rh-stat">
            <div class="rh-stat-icon rose"><i class="bi bi-cake2"></i></div>
            <div>
                <div class="rh-stat-val">{{ $cumpleMes->count() }}</div>
                <div class="rh-stat-lbl">Cumpleaños en {{ now()->translatedFormat('F') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="rh-stat">
            <div class="rh-stat-icon amber"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="rh-stat-val">{{ number_format($horasPendientes, 1) }}h</div>
                <div class="rh-stat-lbl">Horas por compensar</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="rh-stat">
            <div class="rh-stat-icon green"><i class="bi bi-calendar2-check"></i></div>
            <div>
                <div class="rh-stat-val">{{ $diasEconDisp }}</div>
                <div class="rh-stat-lbl">Días econ. disponibles</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="rh-stat">
            <div class="rh-stat-icon indigo"><i class="bi bi-umbrella"></i></div>
            <div>
                <div class="rh-stat-val">{{ $vacDisponibles }}</div>
                <div class="rh-stat-lbl">Días vacac. disponibles</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="rh-stat">
            <div class="rh-stat-icon teal"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="rh-stat-val">{{ $diasPendientesCount }}</div>
                <div class="rh-stat-lbl">Días pendientes por usar</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Alertas cumpleaños ──────────────────────── --}}
@php
    $hoyAlerts  = $proximos->filter(fn($u) => $u->dias_para_cumple === 0);
    $semAlerts  = $proximos->filter(fn($u) => $u->dias_para_cumple > 0 && $u->dias_para_cumple <= 7);
@endphp
@if($hoyAlerts->isNotEmpty() || $semAlerts->isNotEmpty())
<div class="mb-4">
    @foreach($hoyAlerts as $u)
    <div class="alert-rh cumple-hoy-alert mb-2">
        <i class="bi bi-cake2-fill fs-5"></i>
        <span><strong>¡Hoy es el cumpleaños de {{ $u->name }}!</strong>
        @if($u->fecha_nacimiento) — Cumple {{ $u->edad() }} años @endif
        </span>
        <a href="{{ route('usuarios.show', $u) }}" class="ms-auto btn btn-sm btn-warning py-0 px-2" style="font-size:.78rem;">Ver perfil</a>
    </div>
    @endforeach
    @foreach($semAlerts as $u)
    <div class="alert-rh cumple-near-alert mb-2">
        <i class="bi bi-cake2 fs-5"></i>
        <span>Cumpleaños de <strong>{{ $u->name }}</strong> en <strong>{{ $u->dias_para_cumple }} día(s)</strong>
        ({{ $u->fecha_cumple_proxima->format('d/m') }})</span>
        <a href="{{ route('usuarios.show', $u) }}" class="ms-auto btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.78rem;">Ver perfil</a>
    </div>
    @endforeach
</div>
@endif

<div class="row g-3">

    {{-- ── Cumpleaños próximos 30 días ────────── --}}
    @if($proximos->isNotEmpty())
    <div class="col-lg-4">
        <div class="data-card h-100">
            <div class="data-card-header">
                <div class="header-icon rose"><i class="bi bi-cake2"></i></div>
                Cumpleaños próximos
                <span class="badge ms-auto" style="background:#fce4ec;color:#c62828;font-size:.72rem;">30 días</span>
            </div>
            <div class="p-3 d-flex flex-column gap-2" style="max-height:460px;overflow-y:auto;">
                @foreach($proximos as $u)
                @php
                    $dias  = $u->dias_para_cumple;
                    $clase = $dias === 0 ? 'cumple-hoy' : ($dias <= 7 ? 'cumple-sem' : 'cumple-mes');
                    $chipC = $dias === 0 ? 'hoy' : ($dias <= 7 ? 'near' : 'far');
                    $chipTxt = $dias === 0 ? 'HOY' : ($dias === 1 ? '1 día' : "{$dias} días");
                    $initials = strtoupper(mb_substr($u->name, 0, 1));
                    $bgColor = '#' . substr(md5($u->name), 0, 6);
                @endphp
                <a href="{{ route('usuarios.show', $u) }}" class="cumple-card text-decoration-none" style="color:inherit;">
                    <div class="cumple-avatar {{ $clase }}" style="{{ $dias > 7 ? 'background:var(--bg-card-alt);color:var(--text-main);' : '' }}">
                        {{ $initials }}
                    </div>
                    <div style="min-width:0;flex:1;">
                        <div class="fw-semibold" style="font-size:.88rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u->name }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);">
                            {{ $u->fecha_nacimiento->format('d/m') }}
                            @if($u->fecha_nacimiento) &mdash; {{ $u->edad() + ($dias > 0 ? 1 : 0) }} años @endif
                        </div>
                    </div>
                    <span class="dias-chip {{ $chipC }}">{{ $chipTxt }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Personal por recinto ───────────────── --}}
    <div class="{{ $proximos->isNotEmpty() ? 'col-lg-8' : 'col-12' }}">
        <div class="data-card mb-3">
            <div class="data-card-header">
                <div class="header-icon navy"><i class="bi bi-building"></i></div>
                Personal por recinto
            </div>
            <div class="p-3 d-flex flex-wrap gap-2">
                @foreach($porRecinto->sortKeys() as $recinto => $grupo)
                @php
                    $key = str_replace([' ','á','é','í','ó','ú','Á','É','Í','Ó','Ú'],['','a','e','i','o','u','A','E','I','O','U'], $recinto);
                    $short = match(true) {
                        str_contains($recinto,'Oficina')  => 'Oficinas',
                        str_contains($recinto,'Sin')      => 'Sin',
                        default => $key
                    };
                @endphp
                <button class="recinto-filter-btn recinto-badge recinto-{{ $short }}"
                        data-recinto="{{ $recinto }}" style="border:none;cursor:pointer;">
                    <i class="bi bi-building-fill"></i>
                    {{ $recinto }}
                    <span class="fw-bold">{{ $grupo->count() }}</span>
                </button>
                @endforeach
                <button class="recinto-filter-btn recinto-badge" data-recinto="todos"
                        style="background:var(--accent,#3a7bd5);color:#fff;border:none;cursor:pointer;">
                    Todos <span class="fw-bold">{{ $empleados->count() }}</span>
                </button>
            </div>
        </div>

        {{-- Grid empleados --}}
        <div class="row g-2" id="emp-grid">
            @foreach($empleados as $u)
            @php
                $saldo     = $u->saldoTiempo;
                $diasEcon  = $u->diasEconomicosAnio->first();
                $vacAnio   = $u->vacaciones->first();
                $pendCount = $u->diasPendientesPendientes->count();
                $cumpleDias = $u->dias_para_cumple;

                $recintoKey = match(true) {
                    str_contains($u->recinto ?? '', 'Oficina') => 'Oficinas',
                    default => str_replace([' ','á','é','í','ó','ú'],['','a','e','i','o','u'], $u->recinto ?? 'Sin')
                };
            @endphp
            <div class="col-sm-6 col-xl-4 emp-item" data-recinto="{{ $u->recinto ?? 'Sin recinto' }}">
                <a href="{{ route('usuarios.show', $u) }}" class="emp-card d-block text-decoration-none" style="color:inherit;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="emp-avatar" style="background:{{ '#'.substr(md5($u->id.'x'),0,6) }}22;color:{{ '#'.substr(md5($u->id.'x'),0,6) }};border:2px solid {{ '#'.substr(md5($u->id.'x'),0,6) }}44;">
                            {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                        </div>
                        <div style="min-width:0;flex:1;">
                            <div class="fw-semibold" style="font-size:.88rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u->name }}</div>
                            <div style="font-size:.73rem;color:var(--text-muted);">{{ $u->cargo ?? 'Sin cargo' }}</div>
                        </div>
                        @if($cumpleDias !== null && $cumpleDias <= 7)
                            <span title="Cumpleaños" style="font-size:1.1rem;">🎂</span>
                        @endif
                    </div>

                    @if($u->recinto)
                    <div class="mb-2">
                        <span class="recinto-badge recinto-{{ $recintoKey }}" style="font-size:.72rem;">
                            <i class="bi bi-building-fill"></i> {{ $u->recinto }}
                        </span>
                    </div>
                    @endif

                    <div class="d-flex gap-1">
                        <div class="emp-mini-stat">
                            <div class="emp-mini-val {{ ($saldo?->saldo ?? 0) > 0 ? 'text-warning' : '' }}">
                                {{ number_format($saldo?->saldo ?? 0, 1) }}h
                            </div>
                            <div class="emp-mini-lbl">Horas</div>
                        </div>
                        <div class="emp-mini-stat">
                            <div class="emp-mini-val {{ ($vacAnio?->diasDisponibles() ?? 0) > 0 ? 'text-success' : 'text-muted' }}">
                                {{ $vacAnio?->diasDisponibles() ?? '—' }}
                            </div>
                            <div class="emp-mini-lbl">Vacac.</div>
                        </div>
                        <div class="emp-mini-stat">
                            <div class="emp-mini-val {{ ($diasEcon?->diasPendientes() ?? 0) > 0 ? 'text-primary' : 'text-muted' }}">
                                {{ $diasEcon?->diasPendientes() ?? '—' }}
                            </div>
                            <div class="emp-mini-lbl">Econ.</div>
                        </div>
                        <div class="emp-mini-stat">
                            <div class="emp-mini-val {{ $pendCount > 0 ? 'text-danger' : 'text-muted' }}">
                                {{ $pendCount ?: '0' }}
                            </div>
                            <div class="emp-mini-lbl">Pend.</div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btns  = document.querySelectorAll('.recinto-filter-btn');
    var items = document.querySelectorAll('.emp-item');

    btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var recinto = this.dataset.recinto;

            // Mark active
            btns.forEach(function (b) { b.style.opacity = '.55'; b.style.outline = 'none'; });
            this.style.opacity = '1';
            this.style.outline = '2px solid currentColor';

            // Filter
            items.forEach(function (item) {
                var show = recinto === 'todos' || item.dataset.recinto === recinto;
                item.style.display = show ? '' : 'none';
            });
        });
    });

    // Start with "todos" active
    var todosBtn = document.querySelector('[data-recinto="todos"]');
    if (todosBtn) { todosBtn.style.opacity = '1'; todosBtn.style.outline = '2px solid #fff'; }
});
</script>
@endsection
