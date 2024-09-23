<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\Classe;

class StoreClasseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Classe Validation Errors',
            'data'    => [],
            'errors'  => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filiere'        => 'required|exists:filieres,id',
            'cycle'          => 'required|exists:cycles,id',
            'year'              => 'required|numeric|min:1',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'filiere.required'       => 'Le champ filiere est requis.',
            'cycle.required'         => 'Le champ filiere est requis.',
            'filiere.exists'         => 'Le champ filiere est invalide.',
            'cycle.exists'           => 'Le champ filiere est invalide.',
            'number.unique'          => 'L\'année doit être un entier.',
        ];
    }
}
