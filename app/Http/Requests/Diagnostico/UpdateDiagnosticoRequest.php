<?php

namespace App\Http\Requests\Diagnostico;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiagnosticoRequest extends FormRequest
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
        $diagnosticoId = $this->route('id');

        return [
            'sintomas' => ['sometimes', 'required', 'string'],
            'tipo_diagnostico' => ['sometimes', 'required', 'string'],
            'gravedad' => ['sometimes', 'required', 'string'],
            'observaciones' => ['sometimes', 'nullable', 'string'],
            'cita_id' => [
                'sometimes',
                'required',
                'exists:citas,id_cita',
                'unique:diagnosticos,cita_id,' . $diagnosticoId . ',id'
            ],
            
            // Nested detail_diagnostico rules
            'detalles' => ['nullable', 'array'],
            'detalles.*.id' => ['nullable', 'exists:detalle_diagnostico,id'],
            'detalles.*.observacion' => ['nullable', 'string'],
            'detalles.*.zona_bucal' => ['required', 'string'],
            'detalles.*.diente_id' => ['required', 'exists:dientes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'sintomas.required' => 'Debe ingresar los síntomas del paciente.',
            'sintomas.string' => 'Los síntomas deben ser texto.',

            'tipo_diagnostico.required' => 'Debe ingresar el tipo de diagnóstico.',
            'tipo_diagnostico.string' => 'El tipo de diagnóstico debe ser texto.',

            'gravedad.required' => 'Debe seleccionar la gravedad del diagnóstico.',
            'gravedad.string' => 'La gravedad debe ser texto.',

            'observaciones.string' => 'Las observaciones deben ser texto.',

            'cita_id.required' => 'Debe seleccionar una cita.',
            'cita_id.exists' => 'La cita seleccionada no existe.',
            'cita_id.unique' => 'Esta cita ya tiene un diagnóstico registrado.',

            'detalles.array' => 'Los detalles del diagnóstico deben enviarse en un formato válido.',

            'detalles.*.id.exists' => 'Uno de los detalles del diagnóstico no existe.',

            'detalles.*.observacion.string' => 'La observación del detalle debe ser texto.',

            'detalles.*.zona_bucal.required' => 'Debe ingresar la zona bucal afectada.',
            'detalles.*.zona_bucal.string' => 'La zona bucal debe ser texto.',

            'detalles.*.diente_id.required' => 'Debe seleccionar un diente.',
            'detalles.*.diente_id.exists' => 'El diente seleccionado no existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'sintomas' => 'síntomas',
            'tipo_diagnostico' => 'tipo de diagnóstico',
            'gravedad' => 'gravedad',
            'observaciones' => 'observaciones',
            'cita_id' => 'cita',

            'detalles' => 'detalles del diagnóstico',
            'detalles.*.id' => 'detalle del diagnóstico',
            'detalles.*.observacion' => 'observación del detalle',
            'detalles.*.zona_bucal' => 'zona bucal',
            'detalles.*.diente_id' => 'diente',
        ];
    }
}
