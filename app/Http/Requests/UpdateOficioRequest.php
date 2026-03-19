<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOficioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha'          => 'required|date',
            'hora_inicio'    => 'nullable|date_format:H:i',
            'hora_fin'       => 'nullable|date_format:H:i',
            'nombre_evento'  => 'required|string|max:255',
            'numero_oficio'  => 'nullable|string|max:100', // <-- sin unique
            'cobrado'        => 'nullable|boolean',
            'monto_cobrado'  => 'nullable|numeric|min:0',
            'organizador'    => 'required|string|max:255',
            'foto'           => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}