<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; }
  .header { background: #1a1a2e; color: #fff; padding: 12px 16px; margin-bottom: 14px; border-radius: 4px; }
  .header h1 { font-size: 15px; margin-bottom: 2px; }
  .header .sub { font-size: 9px; color: #aaa; }
  .info-row { display: flex; gap: 30px; margin-bottom: 12px; font-size: 9px; color: #555; }
  .info-row span strong { color: #1a1a2e; }
  .stats { display: flex; gap: 16px; margin-bottom: 14px; }
  .stat-box { flex: 1; border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px 12px; text-align: center; }
  .stat-box .val { font-size: 20px; font-weight: 700; color: #1565c0; }
  .stat-box .lbl { font-size: 8px; color: #888; }
  table { width: 100%; border-collapse: collapse; font-size: 9px; }
  thead th { background: #1a1a2e; color: #fff; padding: 6px 8px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: .04em; }
  tbody td { padding: 6px 8px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
  tbody tr:nth-child(even) td { background: #f9f9fb; }
  .badge-si  { background: #dcfce7; color: #166534; border-radius: 20px; padding: 2px 8px; font-weight: 700; }
  .badge-no  { background: #f1f5f9; color: #64748b; border-radius: 20px; padding: 2px 8px; }
  .footer { margin-top: 18px; font-size: 8px; color: #999; text-align: right; }
  .firma-section { margin-top: 40px; display: flex; gap: 40px; }
  .firma-box { flex: 1; border-top: 1px solid #333; padding-top: 6px; text-align: center; font-size: 9px; color: #555; }
</style>
</head>
<body>

<div class="header">
    <h1>{{ $actividad->nombre }}</h1>
    <div class="sub">{{ strtoupper($actividad->tipo) }} &nbsp;·&nbsp; {{ $actividad->codigo }} &nbsp;·&nbsp; Lista de asistentes</div>
</div>

<div class="info-row">
    <span><strong>Fecha:</strong> {{ $actividad->fecha_inicio->format('d/m/Y') }}@if($actividad->fecha_fin && $actividad->fecha_fin != $actividad->fecha_inicio) – {{ $actividad->fecha_fin->format('d/m/Y') }}@endif</span>
    @if($actividad->hora_inicio)<span><strong>Horario:</strong> {{ substr($actividad->hora_inicio,0,5) }}@if($actividad->hora_fin) – {{ substr($actividad->hora_fin,0,5) }}@endif</span>@endif
    @if($actividad->ubicacion)<span><strong>Lugar:</strong> {{ $actividad->ubicacion }}</span>@endif
    @if($actividad->instructor)<span><strong>Instructor:</strong> {{ $actividad->instructor }}</span>@endif
    <span><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</span>
</div>

@php
$totalInscritos  = $inscripciones->count();
$totalAsistieron = $inscripciones->filter(fn($i) => $i->checkin)->count();
$porcentaje      = $totalInscritos > 0 ? round(($totalAsistieron / $totalInscritos) * 100) : 0;
@endphp

<div class="stats">
    <div class="stat-box"><div class="val">{{ $totalInscritos }}</div><div class="lbl">Inscritos</div></div>
    <div class="stat-box"><div class="val">{{ $totalAsistieron }}</div><div class="lbl">Asistieron</div></div>
    <div class="stat-box"><div class="val">{{ $porcentaje }}%</div><div class="lbl">Asistencia</div></div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:28px;">#</th>
            <th style="width:80px;">Folio</th>
            <th style="width:160px;">Nombre completo</th>
            <th style="width:130px;">Email</th>
            <th style="width:90px;">Teléfono</th>
            <th>Institución / Ciudad</th>
            <th style="width:55px;text-align:center;">Asistió</th>
            <th style="width:70px;">Check-in</th>
            <th style="width:90px;">Firma</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inscripciones as $i => $insc)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="font-family:monospace;">{{ $insc->folio }}</td>
            <td><strong>{{ $insc->asistente->nombreCompleto() }}</strong></td>
            <td>{{ $insc->asistente->email ?? '—' }}</td>
            <td>{{ $insc->asistente->telefono ?? '—' }}</td>
            <td>{{ $insc->asistente->institucion ?? '' }}@if($insc->asistente->ciudad) {{ $insc->asistente->ciudad }}@endif</td>
            <td style="text-align:center;">
                @if($insc->checkin)
                    <span class="badge-si">Sí</span>
                @else
                    <span class="badge-no">No</span>
                @endif
            </td>
            <td>{{ $insc->checkin ? $insc->checkin->hora_checkin->format('H:i') : '' }}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="firma-section">
    <div class="firma-box">Responsable del evento</div>
    <div class="firma-box">Coordinación de Cultura</div>
</div>

<div class="footer">Sistema de Cultura &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i') }}</div>

</body>
</html>
