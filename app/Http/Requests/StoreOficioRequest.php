<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOficioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha'          => 'required|date',
            'nombre_evento'  => 'required|string|max:255',
            'numero_oficio'  => 'required|string|max:100|unique:oficios,numero_oficio',
            'cobrado'        => 'required|boolean',
            'monto_cobrado'  => 'nullable|numeric|min:0',
            'organizador'    => 'required|string|max:255',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin'    => 'nullable|date_format:H:i|after:hora_inicio',
            'foto'        => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',

        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required'         => 'La fecha es obligatoria.',
            'nombre_evento.required' => 'El nombre del evento es obligatorio.',
            'numero_oficio.required' => 'El número de oficio es obligatorio.',
            'numero_oficio.unique'   => 'Este número de oficio ya existe.',
            'cobrado.required'       => 'Indica si se cobró o no.',
            'organizador.required'   => 'El organizador es obligatorio.',

        ];
    }
}
