<?php

namespace App\Http\Requests\ServicioPrestado;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServicioPrestadoRequest extends FormRequest
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
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio' => ['required', 'numeric', 'min:0'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'fecha_servicio' => ['required', 'date'],
            'observacion' => ['nullable', 'string', 'max:255'],
            'tratamiento_id' => ['required', 'exists:tratamientos,id'],
            'servicio_id' => ['required', 'exists:servicios,id'],
            'id_boleta' => ['nullable', 'exists:boleta_pago,id_boleta'],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['ACTIVO', 'INACTIVO'])],
        ];
    }
}
