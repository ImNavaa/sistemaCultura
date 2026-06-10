<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte Teatro</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9.5pt; color: #1a1a2e; }

  .header { background: #1a1a2e; color: #fff; padding: 14px 20px; display: flex; justify-content: space-between; align-items: flex-start; }
  .header-title { font-size: 15pt; font-weight: bold; letter-spacing: .5px; }
  .header-sub  { font-size: 8.5pt; opacity: .8; margin-top: 3px; }
  .header-right { text-align: right; font-size: 8pt; opacity: .85; }

  .periodo { background: #e8eaf6; border-left: 4px solid #3949ab; padding: 6px 16px; font-size: 9pt; margin: 10px 0 8px; }
  .periodo strong { color: #3949ab; }

  table { width: 100%; border-collapse: collapse; margin-top: 4px; }
  thead tr { background: #3949ab; color: #fff; }
  thead th { padding: 7px 8px; text-align: left; font-size: 8.5pt; font-weight: 600; white-space: nowrap; }
  tbody tr:nth-child(even) { background: #f5f5f5; }
  tbody tr:nth-child(odd)  { background: #ffffff; }
  tbody td { padding: 6px 8px; font-size: 8.5pt; vertical-align: middle; border-bottom: 1px solid #e8e8e8; }

  .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 7.5pt; font-weight: 600; }
  .b-oficio  { background: #dbeafe; color: #1d4ed8; }
  .b-recibo  { background: #ede9fe; color: #6d28d9; }
  .b-ambos   { background: #fef9c3; color: #854d0e; }
  .b-ninguno { background: #f3f4f6; color: #6b7280; }
  .b-si      { background: #dcfce7; color: #15803d; }
  .b-no      { background: #fee2e2; color: #b91c1c; }

  .monto  { font-weight: 700; color: #166534; }
  .importe{ font-weight: 700; color: #6d28d9; }
  .hora   { white-space: nowrap; color: #374151; }
  .folio  { font-family: monospace; font-size: 8pt; color: #374151; }

  .footer { margin-top: 14px; border-top: 1px solid #ddd; padding-top: 7px; font-size: 7.5pt; color: #888; display: flex; justify-content: space-between; }

  .empty { text-align: center; padding: 30px; color: #888; font-style: italic; }
  .total-row { background: #e8eaf6 !important; font-weight: bold; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="header-title">🎭 Teatro &mdash; Reporte de Eventos</div>
    <div class="header-sub">Sistema de Gestión Cultural</div>
  </div>
  <div class="header-right">
    Generado: {{ now()->format('d/m/Y H:i') }}<br>
    Total: {{ $eventos->count() }} evento(s)
  </div>
</div>

<div class="periodo">
  Período: <strong>{{ $desde->format('d/m/Y') }}</strong>
  &nbsp;al&nbsp;
  <strong>{{ $hasta->format('d/m/Y') }}</strong>
</div>

@if($eventos->isEmpty())
  <div class="empty">No se encontraron eventos en el período seleccionado.</div>
@else
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Fecha</th>
      <th>Hora</th>
      <th>Evento</th>
      <th>Organizador</th>
      <th>Autoriza</th>
      <th>Tipo</th>
      <th>No. Oficio</th>
      <th>Cobrado</th>
      <th>Monto</th>
      <th>No. Recibo</th>
      <th>Importe</th>
    </tr>
  </thead>
  <tbody>
    @foreach($eventos as $i => $evento)
    @php
      $oficio = $evento->oficio;
      $recibo = $evento->recibo;
      $cobrado = $oficio?->cobrado;
      $monto   = $oficio?->monto_cobrado;
      $importe = $recibo?->importe;
    @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td style="white-space:nowrap">{{ $evento->fecha->format('d/m/Y') }}</td>
      <td class="hora">
        @if($evento->hora_inicio)
          {{ substr($evento->hora_inicio, 0, 5) }}
          @if($evento->hora_fin) – {{ substr($evento->hora_fin, 0, 5) }} @endif
        @elseif($oficio?->hora_inicio)
          {{ substr($oficio->hora_inicio, 0, 5) }}
          @if($oficio->hora_fin) – {{ substr($oficio->hora_fin, 0, 5) }} @endif
        @else
          —
        @endif
      </td>
      <td>{{ $evento->nombre_evento }}</td>
      <td>{{ $evento->organizador ?? '—' }}</td>
      <td>{{ $evento->autoriza ?? '—' }}</td>
      <td>
        @php $tipo = $evento->tipo; @endphp
        <span class="badge b-{{ $tipo }}">
          {{ ['oficio'=>'Oficio','recibo'=>'Recibo','ambos'=>'Ambos','ninguno'=>'Ninguno'][$tipo] ?? $tipo }}
        </span>
      </td>
      <td class="folio">{{ $oficio?->numero_oficio ?? '—' }}</td>
      <td>
        @if($oficio)
          <span class="badge {{ $cobrado ? 'b-si' : 'b-no' }}">{{ $cobrado ? 'Sí' : 'No' }}</span>
        @else —
        @endif
      </td>
      <td class="monto">
        @if($monto) ${{ number_format($monto, 2) }} @else — @endif
      </td>
      <td class="folio">{{ $recibo?->numero_recibo ?? '—' }}</td>
      <td class="importe">
        @if($importe) ${{ number_format($importe, 2) }} @else — @endif
      </td>
    </tr>
    @endforeach

    @php
      $totalMonto   = $eventos->sum(fn($e) => $e->oficio?->monto_cobrado ?? 0);
      $totalImporte = $eventos->sum(fn($e) => $e->recibo?->importe ?? 0);
    @endphp
    <tr class="total-row">
      <td colspan="9" style="text-align:right">Totales:</td>
      <td class="monto">${{ number_format($totalMonto, 2) }}</td>
      <td></td>
      <td class="importe">${{ number_format($totalImporte, 2) }}</td>
    </tr>
  </tbody>
</table>
@endif

<div class="footer">
  <span>Sistema de Gestión Cultural &mdash; Teatro</span>
  <span>{{ now()->format('d/m/Y H:i:s') }}</span>
</div>

</body>
</html>
