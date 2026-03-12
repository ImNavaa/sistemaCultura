<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Entrega;

class AlmacenService
{
    // Registra una entrega y descuenta del inventario automáticamente
    public function registrarEntrega(array $datos): Entrega
    {
        $articulo = Articulo::findOrFail($datos['articulo_id']);

        if (!$articulo->tieneSuficiente($datos['cantidad'])) {
            throw new \Exception(
                "Stock insuficiente. Disponible: {$articulo->cantidad_actual} {$articulo->unidad}."
            );
        }

        $entrega = Entrega::create($datos);

        // Descuenta automáticamente del inventario
        $articulo->decrement('cantidad_actual', $datos['cantidad']);

        return $entrega;
    }

    // Cancela una entrega y devuelve el stock
    public function cancelarEntrega(Entrega $entrega): void
    {
        $entrega->articulo->increment('cantidad_actual', $entrega->cantidad);
        $entrega->delete();
    }

    // Agrega stock a un artículo existente
    public function agregarStock(Articulo $articulo, float $cantidad): void
    {
        $articulo->increment('cantidad_actual', $cantidad);
    }
}