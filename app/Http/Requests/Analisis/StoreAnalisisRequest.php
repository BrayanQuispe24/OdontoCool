<?php

namespace App\Http\Requests\Analisis;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnalisisRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['sometimes', 'required', 'string', 'in:ACTIVO,INACTIVO,activo,inactivo'],
        ];
    }
    public function messages(): array
    {
        return [
            // Nombre
            'nombre.required' => 'Debe ingresar el nombre del análisis.',
            'nombre.string' => 'El nombre del análisis debe ser texto.',
            'nombre.max' => 'El nombre del análisis no debe superar los 255 caracteres.',

            // Descripción
            'descripcion.string' => 'La descripción del análisis debe ser texto.',

            // Estado
            'estado.required' => 'Debe seleccionar el estado del análisis.',
            'estado.string' => 'El estado del análisis debe ser texto.',
            'estado.in' => 'El estado seleccionado no es válido. Debe ser ACTIVO o INACTIVO.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del análisis',
            'descripcion' => 'descripción del análisis',
            'estado' => 'estado del análisis',
        ];
    }
}
