<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Entrega;
use App\Models\EntregaDetalle;
use Illuminate\Support\Facades\DB;

class AlmacenService
{
    /**
     * Registra una entrega con uno o varios artículos.
     *
     * $cabecera: folio, receptor, unidad_solicitante, fecha_entrega, responsable_id, observaciones
     * $items: [['articulo_id' => X, 'cantidad' => Y], ...]
     */
    public function registrarEntrega(array $cabecera, array $items): Entrega
    {
        return DB::transaction(function () use ($cabecera, $items) {
            // Validar stock de todos los artículos antes de modificar nada
            foreach ($items as $item) {
                $articulo = Articulo::findOrFail($item['articulo_id']);
                if (!$articulo->tieneSuficiente($item['cantidad'])) {
                    throw new \Exception(
                        "Stock insuficiente para \"{$articulo->nombre}\". Disponible: {$articulo->cantidad_actual} {$articulo->unidad}."
                    );
                }
            }

            $entrega = Entrega::create($cabecera);

            foreach ($items as $item) {
                $articulo = Articulo::find($item['articulo_id']);
                EntregaDetalle::create([
                    'entrega_id'  => $entrega->id,
                    'articulo_id' => $item['articulo_id'],
                    'cantidad'    => $item['cantidad'],
                ]);
                $articulo->decrement('cantidad_actual', $item['cantidad']);
            }

            return $entrega;
        });
    }

    public function cancelarEntrega(Entrega $entrega): void
    {
        DB::transaction(function () use ($entrega) {
            foreach ($entrega->detalles as $detalle) {
                $detalle->articulo->increment('cantidad_actual', $detalle->cantidad);
            }
            $entrega->delete();
        });
    }

    public function agregarStock(Articulo $articulo, float $cantidad): void
    {
        $articulo->increment('cantidad_actual', $cantidad);
    }
}
