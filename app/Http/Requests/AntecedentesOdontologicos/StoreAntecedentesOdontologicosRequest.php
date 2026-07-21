<?php

namespace App\Http\Requests\AntecedentesOdontologicos;

use Illuminate\Foundation\Http\FormRequest;

class StoreAntecedentesOdontologicosRequest extends FormRequest
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
            'observacion_general' => ['nullable', 'string'],
            'fecha_registro' => ['required', 'date'],
            'codigo_historial' => ['required', 'exists:historiales_clinicos,codigo_historial', 'unique:antecedentes_odontologicos,codigo_historial'],
            'detalles' => ['nullable', 'array'],
            'detalles.*.nombre_tratamiento' => ['required', 'string'],
            'detalles.*.descripcion' => ['nullable', 'string'],
            'detalles.*.fecha_tratamiento' => ['required', 'date'],
            'detalles.*.lugar_tratamiento' => ['required', 'string'],
            'detalles.*.observacion' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            // Historial
            'fecha_registro.required' => 'Debe ingresar la fecha de registro.',
            'fecha_registro.date' => 'La fecha de registro no es válida.',

            'codigo_historial.required' => 'Debe seleccionar un historial clínico.',
            'codigo_historial.exists' => 'El historial clínico seleccionado no existe.',
            'codigo_historial.unique' => 'Este historial clínico ya tiene antecedentes odontológicos registrados.',

            // Observación general
            'observacion_general.string' => 'La observación general debe ser texto.',

            // Detalles
            'detalles.array' => 'Los antecedentes odontológicos deben enviarse en un formato válido.',

            'detalles.*.nombre_tratamiento.required' => 'Debe ingresar el nombre del tratamiento.',
            'detalles.*.nombre_tratamiento.string' => 'El nombre del tratamiento debe ser texto.',

            'detalles.*.descripcion.string' => 'La descripción del tratamiento debe ser texto.',

            'detalles.*.fecha_tratamiento.required' => 'Debe ingresar la fecha del tratamiento.',
            'detalles.*.fecha_tratamiento.date' => 'La fecha del tratamiento no es válida.',

            'detalles.*.lugar_tratamiento.required' => 'Debe ingresar el lugar donde se realizó el tratamiento.',
            'detalles.*.lugar_tratamiento.string' => 'El lugar del tratamiento debe ser texto.',

            'detalles.*.observacion.string' => 'La observación del tratamiento debe ser texto.',
        ];
    }

    public function attributes(): array
    {
        return [
            'observacion_general' => 'observación general',
            'fecha_registro' => 'fecha de registro',
            'codigo_historial' => 'historial clínico',

            'detalles' => 'antecedentes odontológicos',

            'detalles.*.nombre_tratamiento' => 'nombre del tratamiento',
            'detalles.*.descripcion' => 'descripción',
            'detalles.*.fecha_tratamiento' => 'fecha del tratamiento',
            'detalles.*.lugar_tratamiento' => 'lugar del tratamiento',
            'detalles.*.observacion' => 'observación',
        ];
    }
}
