<?php

namespace App\Http\Requests\Cita;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCitaRequest extends FormRequest
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
            'fecha_cita' => ['sometimes', 'required', 'date'],
            'hora_inicio' => ['sometimes', 'required', 'date_format:H:i'],
            'hora_fin' => ['sometimes', 'required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo' => ['sometimes', 'required', 'string', 'max:255'],
            'observacion' => ['sometimes', 'nullable', 'string'],
            'paciente_ci' => ['sometimes', 'required', 'string', 'max:15', Rule::exists('pacientes', 'codigo_paciente')],
            'codigo_historial' => ['sometimes', 'nullable', 'integer', Rule::exists('historiales_clinicos', 'codigo_historial')],
            'secretaria_ci' => ['sometimes', 'nullable', 'string', 'max:15', Rule::exists('secretarias', 'codigo_secretaria')],
            'doctor_ci' => ['sometimes', 'required', 'string', 'max:15', Rule::exists('doctores', 'codigo_doctor')],
            'estado_cita_id' => ['sometimes', 'required', 'integer', Rule::exists('estado_cita', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_cita.required' => 'Debe seleccionar la fecha de la cita.',
            'fecha_cita.date' => 'La fecha de la cita no es válida.',

            'hora_inicio.required' => 'Debe seleccionar la hora de inicio.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:mm.',

            'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:mm.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',

            'motivo.required' => 'Debe ingresar el motivo de la cita.',
            'motivo.string' => 'El motivo de la cita debe ser texto.',
            'motivo.max' => 'El motivo de la cita no debe superar los 255 caracteres.',

            'observacion.string' => 'La observación debe ser texto.',

            'paciente_ci.required' => 'Debe seleccionar un paciente.',
            'paciente_ci.string' => 'El paciente seleccionado no es válido.',
            'paciente_ci.max' => 'El código del paciente no debe superar los 15 caracteres.',
            'paciente_ci.exists' => 'El paciente seleccionado no existe.',

            'codigo_historial.integer' => 'El historial clínico seleccionado no es válido.',
            'codigo_historial.exists' => 'El historial clínico seleccionado no existe.',

            'secretaria_ci.string' => 'La secretaria seleccionada no es válida.',
            'secretaria_ci.max' => 'El código de la secretaria no debe superar los 15 caracteres.',
            'secretaria_ci.exists' => 'La secretaria seleccionada no existe.',

            'doctor_ci.required' => 'Debe seleccionar un doctor.',
            'doctor_ci.string' => 'El doctor seleccionado no es válido.',
            'doctor_ci.max' => 'El código del doctor no debe superar los 15 caracteres.',
            'doctor_ci.exists' => 'El doctor seleccionado no existe.',

            'estado_cita_id.required' => 'Debe seleccionar un estado para la cita.',
            'estado_cita_id.integer' => 'El estado de la cita seleccionado no es válido.',
            'estado_cita_id.exists' => 'El estado de la cita seleccionado no existe.',  
        ];
    }

    public function attributes(): array
    {
        return [
            'fecha_cita' => 'fecha de la cita',
            'hora_inicio' => 'hora de inicio',
            'hora_fin' => 'hora de fin',
            'motivo' => 'motivo',
            'observacion' => 'observación',
            'paciente_ci' => 'paciente',
            'codigo_historial' => 'historial clínico',
            'secretaria_ci' => 'secretaria',
            'doctor_ci' => 'doctor',
            'estado_cita_id' => 'estado de la cita',
        ];
    }
}
