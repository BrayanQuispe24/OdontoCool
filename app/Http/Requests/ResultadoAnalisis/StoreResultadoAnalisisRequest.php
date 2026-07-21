<?php

namespace App\Http\Requests\ResultadoAnalisis;

use Illuminate\Foundation\Http\FormRequest;

class StoreResultadoAnalisisRequest extends FormRequest
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
            'fecha_resultado' => ['required', 'date'],
            'resultado' => ['required', 'string'],
            'observaciones' => ['nullable', 'string'],
            'interpretacion' => ['nullable', 'string'],
            'archivo_resultado' => ['nullable', 'file', 'max:10240'], // max 10MB
            'estado' => ['sometimes', 'required', 'string', 'in:ACTIVO,INACTIVO,activo,inactivo'],
            'solicitud_analisis_id' => ['required', 'exists:solicitud_analisis,id'],
        ];
    }
}
