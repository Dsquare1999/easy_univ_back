<?php

namespace App\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\AlphanumericPassword;

class RegisterRequest extends FormRequest
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
                'message'       => 'Registration Validation Errors',
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8', 'string','regex:/[!@#$%^&*()\-_=+{};:,<.>§~]/', new AlphanumericPassword]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {

        // '*.required'              => 'Le champ :attribute est requis.',
        return [
            '*.string'              => 'Ce champ doit être une chaîne de caractère.',
            '*.required'            => 'Ce champ est requis.',
            
            'email.email'           => "Ce champs doit contenir un email (name@example.com).",
            'email.unique'          => "Cet email est déjà pris.",

            'password.min'          => "Le mot de passe doit contenir au moins :min caractères.",
            'password.regex'        => "Le mot de passe doit contenir au moins un caractère spécial."
        ];
    }
}
