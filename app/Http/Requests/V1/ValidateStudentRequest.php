<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateStudentRequest extends FormRequest
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
            'message' => 'Validate Student validation errors',
            'errors'  => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tag'     => 'required|exists:tags,id',
            'titre'     => 'required|string',
            'student'     => 'required|exists:students,id',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'tag.required'          => "L'ajout du Tag est impératif",
            'tag.exists'            => 'Le Tag ajouté n\'existe pas',
            'student.exists'        => 'L\'étudiant ajouté n\'existe pas',
            'titre.required'        => 'Le Titre de l\'étudiant est impératif',
            'titre.string'          => 'Le Titre de l\'étudiant doit être une chaine de caractères',
        ];
    }

}
