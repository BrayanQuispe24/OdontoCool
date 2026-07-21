<?php

namespace App\Http\Requests\ServicioPrestado;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServicioPrestadoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cantidad' => ['sometimes', 'required', 'integer', 'min:1'],
            'precio' => ['sometimes', 'required', 'numeric', 'min:0'],
            'descuento' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'fecha_servicio' => ['sometimes', 'required', 'date'],
            'observacion' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tratamiento_id' => ['sometimes', 'required', 'exists:tratamientos,id'],
            'servicio_id' => ['sometimes', 'required', 'exists:servicios,id'],
            'id_boleta' => ['sometimes', 'nullable', 'exists:boleta_pago,id_boleta'],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['ACTIVO', 'INACTIVO'])],
        ];
    }
}
