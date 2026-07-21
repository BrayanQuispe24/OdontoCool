<?php

namespace App\Http\Requests\SolicitudAnalisis;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudAnalisisRequest extends FormRequest
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
            'fecha_solicitud' => ['required', 'date'],
            'motivo' => ['nullable', 'string'],
            'estado' => ['sometimes', 'required', 'string', 'in:ACTIVO,INACTIVO,activo,inactivo,PENDIENTE,pendiente,COMPLETADO,completado'],
            'analisis_id' => ['required', 'exists:analisis,id'],
            'tratamiento_id' => ['required', 'exists:tratamientos,id'],
            'doctor_ci' => ['nullable', 'string', 'exists:doctores,codigo_doctor'],
        ];
    }
}
