<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClasseRequest extends FormRequest
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
            'message' => 'Invoice validation errors',
            'errors'  => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        $method = $this->method();
        
        if($method == 'PUT'){
            return [
                'filiere'        => 'required|exists:filieres,id',
                'cycle'          => 'required|exists:cycles,id',
                'year'              => 'required|numeric|min:1',
            ];
        }else{
            return [
                'filiere'        => 'sometimes|required|exists:filieres,id',
                'cycle'          => 'sometimes|required|exists:cycles,id',
                'year'              => 'sometimes|required|numeric|min:1',
            ];
        }
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'filiere.required'       => 'Le champ filiere est requis.',
            'cycle.required'         => 'Le champ filiere est requis.',
            'filiere.exists'         => 'Le champ filiere est invalide.',
            'cycle.exists'              => 'Le champ filiere est invalide.',
            'number.unique'             => 'L\'année doit être un entier.',
        ];
    }

}
