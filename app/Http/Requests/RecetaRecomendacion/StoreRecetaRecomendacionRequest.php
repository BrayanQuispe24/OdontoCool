<?php

namespace App\Http\Requests\RecetaRecomendacion;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecetaRecomendacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fecha_emision' => ['required', 'date'],
            'observacion_general' => ['nullable', 'string'],
            'tratamiento_id' => ['required', 'exists:tratamientos,id'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.descripcion' => ['required', 'string'],
            'detalles.*.dosis' => ['required', 'string'],
            'detalles.*.duracion' => ['required', 'string'],
            'detalles.*.frecuencia' => ['required', 'string'],
        ];
    }
}
