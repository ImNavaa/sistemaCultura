<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte Ágora</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9.5pt; color: #1a1a2e; }

  .header { background: #312e81; color: #fff; padding: 14px 20px; display: flex; justify-content: space-between; align-items: flex-start; }
  .header-title { font-size: 15pt; font-weight: bold; letter-spacing: .5px; }
  .header-sub   { font-size: 8.5pt; opacity: .8; margin-top: 3px; }
  .header-right { text-align: right; font-size: 8pt; opacity: .85; }

  .periodo { background: #ede9fe; border-left: 4px solid #7c3aed; padding: 6px 16px; font-size: 9pt; margin: 10px 0 8px; }
  .periodo strong { color: #5b21b6; }

  table { width: 100%; border-collapse: collapse; margin-top: 4px; }
  thead tr { background: #4c1d95; color: #fff; }
  thead th { padding: 7px 8px; text-align: left; font-size: 8.5pt; font-weight: 600; white-space: nowrap; }
  tbody tr:nth-child(even) { background: #f5f3ff; }
  tbody tr:nth-child(odd)  { background: #ffffff; }
  tbody td { padding: 6px 8px; font-size: 8.5pt; vertical-align: middle; border-bottom: 1px solid #ede9fe; }

  .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 7.5pt; font-weight: 600; }
  .b-evento      { background: #e0e7ff; color: #3730a3; }
  .b-fotografia  { background: #d1fae5; color: #065f46; }
  .b-area        { background: #fef3c7; color: #92400e; }
  .b-confirmado  { background: #dcfce7; color: #15803d; }
  .b-tentativo   { background: #fef9c3; color: #854d0e; }
  .b-cancelado   { background: #fee2e2; color: #b91c1c; }

  .hora   { white-space: nowrap; color: #374151; }
  .tel    { font-size: 8pt; color: #6b7280; }
  .areas  { font-size: 7.5pt; color: #5b21b6; }

  .footer { margin-top: 14px; border-top: 1px solid #ddd; padding-top: 7px; font-size: 7.5pt; color: #888; display: flex; justify-content: space-between; }
  .empty  { text-align: center; padding: 30px; color: #888; font-style: italic; }

  /* Resumen por tipo al final */
  .resumen { margin-top: 14px; padding: 10px 16px; background: #f5f3ff; border-radius: 6px; font-size: 8.5pt; }
  .resumen strong { color: #4c1d95; }
  .res-grid { display: flex; gap: 30px; margin-top: 6px; }
  .res-item { text-align: center; }
  .res-num  { font-size: 14pt; font-weight: bold; color: #4c1d95; }
  .res-lbl  { font-size: 7.5pt; color: #6b7280; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="header-title">&#127967; Ágora &mdash; Reporte de Reservaciones</div>
    <div class="header-sub">Sistema de Gestión Cultural</div>
  </div>
  <div class="header-right">
    Generado: {{ now()->format('d/m/Y H:i') }}<br>
    Total: {{ $reservas->count() }} reserva(s)
  </div>
</div>

<div class="periodo">
  Período: <strong>{{ $desde->format('d/m/Y') }}</strong>
  &nbsp;al&nbsp;
  <strong>{{ $hasta->format('d/m/Y') }}</strong>
</div>

@if($reservas->isEmpty())
  <div class="empty">No se encontraron reservaciones en el período seleccionado.</div>
@else
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Fecha</th>
      <th>Hora</th>
      <th>Tipo</th>
      <th>Título / Evento</th>
      <th>Organizador</th>
      <th>Responsable</th>
      <th>Teléfono</th>
      <th>Áreas</th>
      <th>Estado</th>
      <th>Notas</th>
    </tr>
  </thead>
  <tbody>
    @foreach($reservas as $i => $r)
    @php
      $areaIds   = is_array($r->areas_ids) ? $r->areas_ids : [];
      $areaNombres = collect($areaIds)
          ->map(fn($id) => $areas->get($id)?->nombre)
          ->filter()
          ->implode(', ');
    @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td style="white-space:nowrap">{{ $r->fecha->format('d/m/Y') }}</td>
      <td class="hora">
        @if($r->hora_inicio) {{ substr($r->hora_inicio, 0, 5) }} @endif
        @if($r->hora_fin) – {{ substr($r->hora_fin, 0, 5) }} @endif
        @if(!$r->hora_inicio && !$r->hora_fin) — @endif
      </td>
      <td>
        <span class="badge b-{{ $r->tipo }}">
          {{ ['evento'=>'Evento','fotografia'=>'Fotografía','area'=>'Área(s)'][$r->tipo] ?? $r->tipo }}
        </span>
      </td>
      <td>{{ $r->titulo }}</td>
      <td>{{ $r->organizador ?? '—' }}</td>
      <td>{{ $r->responsable ?? '—' }}</td>
      <td class="tel">{{ $r->telefono_contacto ?? '—' }}</td>
      <td class="areas">
        @if($r->tipo === 'area' && $areaNombres)
          {{ $areaNombres }}
        @elseif($r->tipo !== 'area')
          <span style="color:#9ca3af">Todo el Ágora</span>
        @else
          —
        @endif
      </td>
      <td>
        <span class="badge b-{{ $r->estado }}">
          {{ ['confirmado'=>'Confirmado','tentativo'=>'Tentativo','cancelado'=>'Cancelado'][$r->estado] ?? $r->estado }}
        </span>
      </td>
      <td style="font-size:7.5pt;color:#6b7280">{{ $r->notas_internas ?? '' }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

@php
  $totEvento      = $reservas->where('tipo','evento')->count();
  $totFotografia  = $reservas->where('tipo','fotografia')->count();
  $totArea        = $reservas->where('tipo','area')->count();
  $totConfirmado  = $reservas->where('estado','confirmado')->count();
  $totTentativo   = $reservas->where('estado','tentativo')->count();
  $totCancelado   = $reservas->where('estado','cancelado')->count();
@endphp
<div class="resumen">
  <strong>Resumen del período</strong>
  <div class="res-grid">
    <div class="res-item"><div class="res-num">{{ $totEvento }}</div><div class="res-lbl">Evento</div></div>
    <div class="res-item"><div class="res-num">{{ $totFotografia }}</div><div class="res-lbl">Fotografía</div></div>
    <div class="res-item"><div class="res-num">{{ $totArea }}</div><div class="res-lbl">Área(s)</div></div>
    <div style="width:1px;background:#c4b5fd;margin:0 10px"></div>
    <div class="res-item"><div class="res-num" style="color:#15803d">{{ $totConfirmado }}</div><div class="res-lbl">Confirmadas</div></div>
    <div class="res-item"><div class="res-num" style="color:#854d0e">{{ $totTentativo }}</div><div class="res-lbl">Tentativas</div></div>
    <div class="res-item"><div class="res-num" style="color:#b91c1c">{{ $totCancelado }}</div><div class="res-lbl">Canceladas</div></div>
  </div>
</div>
@endif

<div class="footer">
  <span>Sistema de Gestión Cultural &mdash; Ágora</span>
  <span>{{ now()->format('d/m/Y H:i:s') }}</span>
</div>

</body>
</html>
