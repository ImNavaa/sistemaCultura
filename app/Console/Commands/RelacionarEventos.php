<?php

namespace App\Console\Commands;

use App\Models\Evento;
use App\Models\Oficio;
use App\Models\Recibo;
use Illuminate\Console\Command;

class RelacionarEventos extends Command
{
    protected $signature   = 'eventos:relacionar';
    protected $description = 'Relaciona eventos existentes con sus oficios y recibos por nombre y fecha';

    public function handle()
    {
        $eventos = Evento::all();
        $relacionados = 0;
        $noEncontrados = [];

        foreach ($eventos as $evento) {
            $actualizado = false;

            // Buscar oficio por nombre y fecha
            $oficio = Oficio::where('nombre_evento', $evento->nombre_evento)
                ->whereDate('fecha', $evento->fecha)
                ->first();

            if ($oficio && !$oficio->evento_id) {
                $oficio->update(['evento_id' => $evento->id]);
                $this->info("✅ Oficio relacionado: {$evento->nombre_evento} ({$evento->fecha->format('Y-m-d')})");
                $actualizado = true;
            }

            // Buscar recibo por nombre y fecha
            $recibo = Recibo::where('nombre_evento', $evento->nombre_evento)
                ->whereDate('fecha', $evento->fecha)
                ->first();

            if ($recibo && !$recibo->evento_id) {
                $recibo->update(['evento_id' => $evento->id]);
                $this->info("✅ Recibo relacionado: {$evento->nombre_evento} ({$evento->fecha->format('Y-m-d')})");
                $actualizado = true;
            }

            if ($actualizado) {
                $relacionados++;
            } else {
                $noEncontrados[] = "{$evento->nombre_evento} ({$evento->fecha->format('Y-m-d')})";
            }
        }

        $this->info("\n✅ Total relacionados: {$relacionados}");

        if (count($noEncontrados) > 0) {
            $this->warn("\n⚠️ Eventos sin oficio ni recibo encontrado:");
            foreach ($noEncontrados as $item) {
                $this->warn("   - {$item}");
            }
        }

        return Command::SUCCESS;
    }
}