<?php

namespace App\Http\Requests\HistorialClinico;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHistorialClinicoRequest extends FormRequest
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
        $historialId = $this->route('codigo_historial') ?? $this->route('id');

        return [
            'alergias' => ['sometimes', 'nullable', 'string'],
            'antecedentes_medicos' => ['sometimes', 'nullable', 'string'],
            'enfermedades_base' => ['sometimes', 'nullable', 'string'],
            'motivo_apertura' => ['sometimes', 'required', 'string'],
            'fecha_apertura' => ['sometimes', 'required', 'date'],
            'fecha_actualizacion' => ['sometimes', 'nullable', 'date'],
            'observaciones_generales' => ['sometimes', 'nullable', 'string'],
            'estado' => ['sometimes', 'required', 'string', 'in:ACTIVO,INACTIVO,activo,inactivo'],
            'paciente_ci' => [
                'sometimes', 
                'required', 
                'exists:pacientes,codigo_paciente', 
                'unique:historiales_clinicos,paciente_ci,' . $historialId . ',codigo_historial'
            ],
            
            // Nested dental antecedent & details rules
            'antecedentes_odontologicos' => ['nullable', 'array'],
            'antecedentes_odontologicos.id_antecedente' => ['nullable', 'exists:antecedentes_odontologicos,id_antecedente'],
            'antecedentes_odontologicos.fecha_registro' => ['required_with:antecedentes_odontologicos', 'date'],
            'antecedentes_odontologicos.observacion_general' => ['nullable', 'string'],
            'antecedentes_odontologicos.detalles' => ['nullable', 'array'],
            'antecedentes_odontologicos.detalles.*.id_detalle_antecedente' => ['nullable', 'exists:detalle_antecedentes_odontologicos,id_detalle_antecedente'],
            'antecedentes_odontologicos.detalles.*.nombre_tratamiento' => ['required', 'string'],
            'antecedentes_odontologicos.detalles.*.descripcion' => ['nullable', 'string'],
            'antecedentes_odontologicos.detalles.*.fecha_tratamiento' => ['required', 'date'],
            'antecedentes_odontologicos.detalles.*.lugar_tratamiento' => ['required', 'string'],
            'antecedentes_odontologicos.detalles.*.observacion' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'alergias.string' => 'Las alergias deben ser texto.',
            'antecedentes_medicos.string' => 'Los antecedentes médicos deben ser texto.',
            'enfermedades_base.string' => 'Las enfermedades base deben ser texto.',

            'motivo_apertura.required' => 'Debe ingresar el motivo de apertura del historial clínico.',
            'motivo_apertura.string' => 'El motivo de apertura debe ser texto.',

            'fecha_apertura.required' => 'Debe ingresar la fecha de apertura.',
            'fecha_apertura.date' => 'La fecha de apertura no es válida.',

            'fecha_actualizacion.date' => 'La fecha de actualización no es válida.',

            'observaciones_generales.string' => 'Las observaciones generales deben ser texto.',

            'estado.required' => 'Debe seleccionar el estado del historial clínico.',
            'estado.string' => 'El estado debe ser texto.',
            'estado.in' => 'El estado seleccionado no es válido. Debe ser ACTIVO o INACTIVO.',

            'paciente_ci.required' => 'Debe seleccionar un paciente.',
            'paciente_ci.exists' => 'El paciente seleccionado no existe.',
            'paciente_ci.unique' => 'Este paciente ya tiene un historial clínico registrado.',

            'antecedentes_odontologicos.array' => 'Los antecedentes odontológicos deben enviarse en un formato válido.',

            'antecedentes_odontologicos.id_antecedente.exists' => 'El antecedente odontológico seleccionado no existe.',

            'antecedentes_odontologicos.fecha_registro.required_with' => 'Debe ingresar la fecha de registro de los antecedentes odontológicos.',
            'antecedentes_odontologicos.fecha_registro.date' => 'La fecha de registro de los antecedentes odontológicos no es válida.',

            'antecedentes_odontologicos.observacion_general.string' => 'La observación general de los antecedentes odontológicos debe ser texto.',

            'antecedentes_odontologicos.detalles.array' => 'Los detalles de antecedentes odontológicos deben enviarse en un formato válido.',

            'antecedentes_odontologicos.detalles.*.id_detalle_antecedente.exists' => 'Uno de los detalles de antecedentes odontológicos no existe.',

            'antecedentes_odontologicos.detalles.*.nombre_tratamiento.required' => 'Debe ingresar el nombre del tratamiento odontológico.',
            'antecedentes_odontologicos.detalles.*.nombre_tratamiento.string' => 'El nombre del tratamiento odontológico debe ser texto.',

            'antecedentes_odontologicos.detalles.*.descripcion.string' => 'La descripción del tratamiento odontológico debe ser texto.',

            'antecedentes_odontologicos.detalles.*.fecha_tratamiento.required' => 'Debe ingresar la fecha del tratamiento odontológico.',
            'antecedentes_odontologicos.detalles.*.fecha_tratamiento.date' => 'La fecha del tratamiento odontológico no es válida.',

            'antecedentes_odontologicos.detalles.*.lugar_tratamiento.required' => 'Debe ingresar el lugar donde se realizó el tratamiento odontológico.',
            'antecedentes_odontologicos.detalles.*.lugar_tratamiento.string' => 'El lugar del tratamiento odontológico debe ser texto.',

            'antecedentes_odontologicos.detalles.*.observacion.string' => 'La observación del tratamiento odontológico debe ser texto.',
        ];
    }

    public function attributes(): array
    {
        return [
            'alergias' => 'alergias',
            'antecedentes_medicos' => 'antecedentes médicos',
            'enfermedades_base' => 'enfermedades base',
            'motivo_apertura' => 'motivo de apertura',
            'fecha_apertura' => 'fecha de apertura',
            'fecha_actualizacion' => 'fecha de actualización',
            'observaciones_generales' => 'observaciones generales',
            'estado' => 'estado',
            'paciente_ci' => 'paciente',

            'antecedentes_odontologicos' => 'antecedentes odontológicos',
            'antecedentes_odontologicos.id_antecedente' => 'antecedente odontológico',
            'antecedentes_odontologicos.fecha_registro' => 'fecha de registro de antecedentes odontológicos',
            'antecedentes_odontologicos.observacion_general' => 'observación general de antecedentes odontológicos',
            'antecedentes_odontologicos.detalles' => 'detalles de antecedentes odontológicos',

            'antecedentes_odontologicos.detalles.*.id_detalle_antecedente' => 'detalle del antecedente odontológico',
            'antecedentes_odontologicos.detalles.*.nombre_tratamiento' => 'nombre del tratamiento odontológico',
            'antecedentes_odontologicos.detalles.*.descripcion' => 'descripción del tratamiento odontológico',
            'antecedentes_odontologicos.detalles.*.fecha_tratamiento' => 'fecha del tratamiento odontológico',
            'antecedentes_odontologicos.detalles.*.lugar_tratamiento' => 'lugar del tratamiento odontológico',
            'antecedentes_odontologicos.detalles.*.observacion' => 'observación del tratamiento odontológico',
        ];
    }
}
