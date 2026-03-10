<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReciboRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha'         => 'required|date',
            'nombre_evento' => 'required|string|max:255',
            'importe'       => 'required|numeric|min:0',
            'organizador'   => 'required|string|max:255',
            'concepto'      => 'required|string',
            'foto' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}