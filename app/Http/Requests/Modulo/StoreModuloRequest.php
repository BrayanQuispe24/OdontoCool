<?php

namespace App\Http\Requests\Modulo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreModuloRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;//adicionar verificacion
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre'=>['required','string','max:100',Rule::unique('modulos','nombre')],
            'estado'=>['sometimes','required','string','max:50',Rule::in(['activo','inactivo'])],
        ];
    }
}
