<?php

namespace App\Http\Requests\ResultadoAnalisis;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResultadoAnalisisRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'fecha_resultado' => ['sometimes', 'required', 'date'],
            'resultado' => ['sometimes', 'required', 'string'],
            'observaciones' => ['sometimes', 'nullable', 'string'],
            'interpretacion' => ['sometimes', 'nullable', 'string'],
            'archivo_resultado' => ['sometimes', 'nullable'],
            'estado' => ['sometimes', 'required', 'string', 'in:ACTIVO,INACTIVO,activo,inactivo'],
            'solicitud_analisis_id' => ['sometimes', 'required', 'exists:solicitud_analisis,id'],
        ];
    }
}
