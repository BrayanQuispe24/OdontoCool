<?php

namespace App\Http\Requests\Tratamiento;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTratamientoRequest extends FormRequest
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

        $tratamientoId = $this->route('tratamiento_id') ?? $this->route('id');

        return [
            'objetivo_tratamiento' => ['sometimes', 'required', 'string', 'max:255'],
            'observacion' => ['sometimes', 'nullable', 'string'],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['activo', 'inactivo', 'ACTIVO', 'INACTIVO'])],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin_estimada' => ['sometimes', 'required', 'date', 'after_or_equal:fecha_inicio'],
            'fecha_fin_real' => ['sometimes', 'nullable', 'date', 'after_or_equal:fecha_inicio'],
            'diagnostico_id' => ['sometimes', 'required', 'exists:diagnosticos,id', 'unique:tratamientos,diagnostico_id,' . $tratamientoId . ',id'],
            'codigo_historial' => ['sometimes', 'required', 'exists:historiales_clinicos,codigo_historial'],
            'dientes_tratamientos' => ['required', 'array', 'min:1'],
            'dientes_tratamientos.*.id' => ['sometimes', 'integer', 'exists:tratamientos_dientes,id'],
            'dientes_tratamientos.*.diente_id' => ['required', 'exists:dientes,id'],
            'dientes_tratamientos.*.cara_dental' => ['required', 'string'],
            'dientes_tratamientos.*.observacion' => ['nullable', 'string'],
            'dientes_tratamientos.*.tratamiento_planificado' => ['required', 'string'],
            
            'receta' => ['nullable', 'array'],
            'receta.observacion_general' => ['sometimes', 'nullable', 'string'],
            'receta.detalles' => ['nullable', 'array'],
            'receta.detalles.*.id' => ['sometimes', 'integer', 'exists:detalle_recomendacion,id'],
            'receta.detalles.*.descripcion' => ['required_with:receta.detalles', 'string', 'max:255'],
            'receta.detalles.*.dosis' => ['required_with:receta.detalles', 'string', 'max:255'],
            'receta.detalles.*.duracion' => ['required_with:receta.detalles', 'string', 'max:255'],
            'receta.detalles.*.frecuencia' => ['required_with:receta.detalles', 'string', 'max:255'],

            'servicios_prestados' => ['nullable', 'array'],
            'servicios_prestados.*.id' => ['sometimes', 'integer', 'exists:servicio_prestados,id'],
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
        ];
    }
}
