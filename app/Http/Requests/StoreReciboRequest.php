<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReciboRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha'         => 'required|date',
            'numero_recibo'  => 'nullable|string|max:100',
            'nombre_evento' => 'required|string|max:255',
            'importe'       => 'required|numeric|min:0',
            'organizador'   => 'required|string|max:255',
            'concepto'      => 'required|string',
            'foto' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required'         => 'La fecha es obligatoria.',
            'nombre_evento.required' => 'El nombre del evento es obligatorio.',
            'importe.required'       => 'El importe es obligatorio.',
            'importe.numeric'        => 'El importe debe ser un número.',
            'organizador.required'   => 'El organizador es obligatorio.',
            'concepto.required'      => 'El concepto es obligatorio.',
        ];
    }
}