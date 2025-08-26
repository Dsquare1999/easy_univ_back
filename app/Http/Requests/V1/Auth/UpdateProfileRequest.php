<?php

namespace App\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * On failed validation, throw an exception
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
                'success'       => false,
                'message'       => 'Profile Update Validation Errors',
                'data'          => [],
                'errors'        => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'matricule' => 'sometimes|string|max:50|unique:users,matricule,' . $this->user()->id,
            'email' => 'sometimes|email|unique:users,email,' . $this->user()->id,
            'profile' => 'sometimes|file|image|max:2048',
            'phone' => 'sometimes|string',
            'bio' => 'sometimes|string',
        ];
    }

    public function messages(): array
    {

        return [
            '*.string'              => 'Ce champ doit être une chaîne de caractère.',
            '*.required'            => 'Ce champ est requis.',
            'profile.image'          => 'Le profile doit être une image.',
            'profile.file'          => 'Le profile doit être un fichier.',
            'email.email'           => "Ce champs doit contenir un email (name@example.com).",
        ];
    }
}

