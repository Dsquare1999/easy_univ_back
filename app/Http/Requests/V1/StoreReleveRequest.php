<?php

namespace App\Http\Requests\V1;


use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReleveRequest extends FormRequest
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
            'message' => 'Validation Errors',
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
            'classe'        => 'required|uuid|exists:classes,id',
            'matiere'       => 'required|uuid|exists:matieres,id',
            'student'       => 'sometimes|uuid|exists:students,id',
            'exam1'         => 'nullable|numeric|min:0',    
            'exam2'         => 'nullable|numeric|min:0',    
            'partial'       => 'nullable|numeric|min:0',    
            'remedial'      => 'nullable|numeric|min:0',    
        ];
    }

        /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array
    {
        return [
            'classe.required' => 'La classe est obligatoire.',
            'classe.uuid' => 'L\'identifiant de la classe doit être un UUID valide.',
            'classe.exists' => 'La classe sélectionnée n\'existe pas.',

            'matiere.required' => 'La matière est obligatoire.',
            'matiere.uuid' => 'L\'identifiant de la matière doit être un UUID valide.',
            'matiere.exists' => 'La matière sélectionnée n\'existe pas.',

            'student.exists' => 'La matière sélectionnée n\'existe pas.',
            'exam1.numeric' => 'L\'examination 1 doit être un nombre.',
            'exam2.numeric' => 'L\'examination 2 doit être un nombre.',
            'partial.numeric' => 'La note partielle doit être un nombre.',
            'remedial.numeric' => 'La note de rattrapage doit être un nombre.',

        ];
    }
}
