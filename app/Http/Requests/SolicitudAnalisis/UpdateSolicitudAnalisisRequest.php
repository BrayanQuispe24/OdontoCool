<?php

namespace App\Http\Requests\SolicitudAnalisis;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSolicitudAnalisisRequest extends FormRequest
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
            'fecha_solicitud' => ['sometimes', 'required', 'date'],
            'motivo' => ['sometimes', 'nullable', 'string'],
            'estado' => ['sometimes', 'required', 'string', 'in:ACTIVO,INACTIVO,activo,inactivo,PENDIENTE,pendiente,COMPLETADO,completado'],
            'analisis_id' => ['sometimes', 'required', 'exists:analisis,id'],
            'tratamiento_id' => ['sometimes', 'required', 'exists:tratamientos,id'],
            'doctor_ci' => ['sometimes', 'nullable', 'string', 'exists:doctores,codigo_doctor'],
        ];
    }
}
