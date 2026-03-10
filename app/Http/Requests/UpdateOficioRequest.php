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
        $id = $this->route('oficio')->id ?? $this->route('oficio');

        return [
            'fecha'          => 'required|date',
            'nombre_evento'  => 'required|string|max:255',
            'numero_oficio'  => 'required|string|max:100|unique:oficios,numero_oficio,' . $id,
            'cobrado'        => 'required|boolean',
            'monto_cobrado'  => 'nullable|numeric|min:0',
            'organizador'    => 'required|string|max:255',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin'    => 'nullable|date_format:H:i|after:hora_inicio',
            'foto'        => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}
