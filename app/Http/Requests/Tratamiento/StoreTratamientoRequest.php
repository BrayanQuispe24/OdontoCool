<?php

namespace App\Http\Requests\Tratamiento;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTratamientoRequest extends FormRequest
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
            'objetivo_tratamiento' => ['required', 'string', 'max:255'],
            'observacion' => ['nullable', 'string'],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['activo', 'inactivo', 'ACTIVO', 'INACTIVO'])],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin_estimada' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'fecha_fin_real' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'diagnostico_id' => ['required', 'exists:diagnosticos,id', 'unique:tratamientos,diagnostico_id'],
            'codigo_historial' => ['required', 'exists:historiales_clinicos,codigo_historial'],
            'dientes_tratamientos' => ['required', 'array', 'min:1'],
            'dientes_tratamientos.*.diente_id' => ['required', 'exists:dientes,id'],
            'dientes_tratamientos.*.cara_dental' => ['required', 'string'],
            'dientes_tratamientos.*.observacion' => ['nullable', 'string'],
            'dientes_tratamientos.*.tratamiento_planificado' => ['required', 'string'],
            
            'receta' => ['nullable', 'array'],
            'receta.observacion_general' => ['nullable', 'string'],
            'receta.detalles' => ['nullable', 'array'],
            'receta.detalles.*.descripcion' => ['required_with:receta.detalles', 'string', 'max:255'],
            'receta.detalles.*.dosis' => ['required_with:receta.detalles', 'string', 'max:255'],
            'receta.detalles.*.duracion' => ['required_with:receta.detalles', 'string', 'max:255'],
            'receta.detalles.*.frecuencia' => ['required_with:receta.detalles', 'string', 'max:255'],

            'servicios_prestados' => ['nullable', 'array'],
            'servicios_prestados.*.servicio_id' => ['required', 'exists:servicios,id'],
            'servicios_prestados.*.cantidad' => ['required', 'integer', 'min:1'],
            'servicios_prestados.*.precio' => ['required', 'numeric', 'min:0'],
            'servicios_prestados.*.descuento' => ['nullable', 'numeric', 'min:0'],
            'servicios_prestados.*.fecha_servicio' => ['required', 'date'],
            'servicios_prestados.*.observacion' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'dientes_tratamientos.required' => 'Debe asociar al menos un diente a este tratamiento.',
            'dientes_tratamientos.min' => 'Debe asociar al menos un diente a este tratamiento.',
            'dientes_tratamientos.array' => 'El formato de los tratamientos de dientes es inválido.',
            'fecha_fin_estimada.after_or_equal' => 'La fecha de fin estimada debe ser mayor o igual a la fecha de inicio.',
            'fecha_fin_real.after_or_equal' => 'La fecha de fin real debe ser mayor o igual a la fecha de inicio.',
        ];
    }
}
