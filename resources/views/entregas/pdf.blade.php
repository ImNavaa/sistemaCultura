<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 11px;
    color: #1a1a1a;
    background: white;
}

.pagina {
    padding: 18mm 18mm 14mm 18mm;
    min-height: 270mm;
    display: flex;
    flex-direction: column;
}

/* Encabezado */
.encabezado {
    border-bottom: 3px solid #1a1a2e;
    padding-bottom: 10px;
    margin-bottom: 14px;
}
.enc-tabla { width: 100%; border-collapse: collapse; }
.enc-tabla td { vertical-align: middle; padding: 0 8px; }
.celda-logo  { width: 90px; text-align: center; }
.celda-titulo { text-align: center; }
.celda-folio  { width: 130px; text-align: right; }

.logo-placeholder {
    width: 75px;
    height: 75px;
    border: 2px solid #1a1a2e;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
    color: #555;
    text-align: center;
    line-height: 1.3;
    padding: 4px;
}
.titulo-principal {
    font-size: 16px;
    font-weight: bold;
    color: #1a1a2e;
    letter-spacing: .05em;
    text-transform: uppercase;
}
.subtitulo { font-size: 11px; color: #555; margin-top: 3px; }
.folio-box {
    border: 1.5px solid #1a1a2e;
    border-radius: 5px;
    padding: 8px 10px;
    text-align: center;
    display: inline-block;
    min-width: 120px;
}
.folio-label { font-size: 8px; text-transform: uppercase; color: #777; letter-spacing: .08em; }
.folio-num   { font-size: 15px; font-weight: bold; color: #1a1a2e; margin-top: 2px; }

/* Secciones */
.seccion { margin-bottom: 14px; }
.sec-titulo {
    font-size: 8.5px;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #777;
    font-weight: bold;
    border-bottom: 1px solid #ddd;
    padding-bottom: 3px;
    margin-bottom: 7px;
}
.datos-tabla { width: 100%; border-collapse: collapse; }
.datos-tabla td { padding: 4px 8px; font-size: 10.5px; }
.datos-tabla .etiqueta { font-weight: bold; color: #333; width: 130px; }
.datos-tabla .valor    { color: #1a1a1a; }
.datos-tabla .valor-destacado { font-weight: bold; font-size: 11.5px; color: #1a1a2e; }

/* Tabla de productos */
.tabla-productos { width: 100%; border-collapse: collapse; margin-top: 4px; }
.tabla-productos thead tr { background: #1a1a2e; color: white; }
.tabla-productos thead th {
    padding: 7px 10px;
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    font-weight: bold;
}
.tabla-productos tbody tr { border-bottom: 1px solid #e9ecef; }
.tabla-productos tbody tr:nth-child(even) { background: #f8f9fa; }
.tabla-productos tbody td { padding: 7px 10px; font-size: 10.5px; }
.col-num      { width: 7%;  text-align: center; }
.col-desc     { width: 53%; }
.col-cantidad { width: 20%; text-align: center; }
.col-unidad   { width: 20%; text-align: center; }
.tabla-productos tfoot td {
    padding: 5px 10px;
    font-size: 9px;
    color: #888;
    border-top: 1px solid #dee2e6;
    font-style: italic;
}
.fila-vacia td { height: 24px; }

/* Observaciones */
.obs-box {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 10px;
    min-height: 28px;
    font-size: 10px;
    color: #555;
}

/* Firmas */
.firmas-section { margin-top: auto; padding-top: 20px; }
.firmas-tabla { width: 100%; border-collapse: collapse; }
.firmas-tabla td { width: 33.33%; text-align: center; padding: 0 12px; vertical-align: bottom; }
.firma-espacio { height: 38px; }
.firma-linea {
    border-top: 1.5px solid #1a1a2e;
    margin: 0 10px;
    margin-bottom: 5px;
    padding-top: 4px;
}
.firma-rol {
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #1a1a2e;
}
.firma-nombre { font-size: 9.5px; color: #555; margin-top: 3px; font-style: italic; }

/* Pie */
.pie-pagina {
    margin-top: 16px;
    padding-top: 8px;
    border-top: 1px solid #eee;
    text-align: center;
    font-size: 8px;
    color: #aaa;
}
</style>
</head>
<body>
<div class="pagina">

    {{-- ENCABEZADO --}}
    <div class="encabezado">
        <table class="enc-tabla">
            <tr>
                <td class="celda-logo">
                    <div class="logo-placeholder">Instituto Municipal<br>de Cultura</div>
                </td>
                <td class="celda-titulo">
                    <div class="titulo-principal">Vale de Salida de Almacén</div>
                    <div class="subtitulo">Instituto Municipal de Cultura — Teatro Municipal</div>
                </td>
                <td class="celda-folio">
                    <div class="folio-box">
                        <div class="folio-label">Folio</div>
                        <div class="folio-num">{{ $entrega->folio }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- DATOS GENERALES --}}
    <div class="seccion">
        <div class="sec-titulo">Datos generales</div>
        <table class="datos-tabla">
            <tr>
                <td class="etiqueta">Fecha:</td>
                <td class="valor valor-destacado">
                    {{ $entrega->fecha_entrega->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                </td>
                <td class="etiqueta">Folio:</td>
                <td class="valor valor-destacado">{{ $entrega->folio }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Unidad solicitante:</td>
                <td class="valor valor-destacado" colspan="3">
                    {{ $entrega->unidad_solicitante ?? '—' }}
                </td>
            </tr>
            <tr>
                <td class="etiqueta">Recibe:</td>
                <td class="valor">{{ $entrega->receptor }}</td>
                <td class="etiqueta">Responsable entrega:</td>
                <td class="valor">{{ $entrega->responsable->name }}</td>
            </tr>
        </table>
    </div>

    {{-- TABLA DE ARTÍCULOS --}}
    <div class="seccion">
        <div class="sec-titulo">Artículos entregados</div>
        <table class="tabla-productos">
            <thead>
                <tr>
                    <th class="col-num">No.</th>
                    <th class="col-desc">Descripción del producto</th>
                    <th class="col-cantidad">Cantidad</th>
                    <th class="col-unidad">Unidad de medida</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entrega->detalles as $i => $detalle)
                <tr>
                    <td class="col-num">{{ $i + 1 }}</td>
                    <td class="col-desc">
                        {{ $detalle->articulo->nombre }}
                        @if($detalle->articulo->descripcion)
                            <br><span style="font-size:9px;color:#888;">{{ $detalle->articulo->descripcion }}</span>
                        @endif
                    </td>
                    <td class="col-cantidad" style="font-weight:bold;">
                        {{ number_format($detalle->cantidad, 2) }}
                    </td>
                    <td class="col-unidad">{{ ucfirst($detalle->articulo->unidad) }}</td>
                </tr>
                @endforeach

                {{-- Filas vacías para completar espacio visual (mínimo 4 en total) --}}
                @for($j = $entrega->detalles->count(); $j < 5; $j++)
                <tr class="fila-vacia">
                    <td class="col-num" style="color:#e0e0e0;">{{ $j + 1 }}</td>
                    <td class="col-desc"></td>
                    <td class="col-cantidad"></td>
                    <td class="col-unidad"></td>
                </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        * Este documento ampara únicamente los artículos listados arriba.
                        Total de conceptos: {{ $entrega->detalles->count() }}.
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- OBSERVACIONES --}}
    @if($entrega->observaciones)
    <div class="seccion">
        <div class="sec-titulo">Observaciones</div>
        <div class="obs-box">{{ $entrega->observaciones }}</div>
    </div>
    @endif

    {{-- FIRMAS --}}
    <div class="firmas-section">
        <div class="sec-titulo">Firmas de conformidad</div>
        <br>
        <table class="firmas-tabla">
            <tr>
                <td>
                    <div class="firma-espacio"></div>
                    <div class="firma-linea"></div>
                    <div class="firma-rol">Autoriza</div>
                    <div class="firma-nombre">Director Alfredo Castellanos Gutiérrez</div>
                </td>
                <td>
                    <div class="firma-espacio"></div>
                    <div class="firma-linea"></div>
                    <div class="firma-rol">Entrega</div>
                    <div class="firma-nombre">&nbsp;</div>
                </td>
                <td>
                    <div class="firma-espacio"></div>
                    <div class="firma-linea"></div>
                    <div class="firma-rol">Recibe</div>
                    <div class="firma-nombre">&nbsp;</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- PIE --}}
    <div class="pie-pagina">
        Documento generado el {{ now()->format('d/m/Y H:i') }} •
        Sistema de Control de Inventarios — Instituto Municipal de Cultura •
        Folio: {{ $entrega->folio }}
    </div>

</div>
</body>
</html>
