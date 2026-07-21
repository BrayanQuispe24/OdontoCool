<?php

namespace App\Http\Requests\Permiso;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermisoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;// aplicar validación de roles
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre'=>['sometimes','required','string','max:100'],
            'estado'=>['sometimes','required','string','max:50'],
            'modulo_id'=>['sometimes','required',Rule::exists('modulos','id')]
        ];
    }
}
