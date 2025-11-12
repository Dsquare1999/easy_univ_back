<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class StoreUserRequest extends FormRequest
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
            'message' => 'User Validation Errors',
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
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'matricule' => ['required', 'string', 'max:255', 'unique:users'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'type' => ['sometimes', 'string'],
            'birthdate' => ['nullable', 'date'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'sexe'    => ['nullable', 'string', 'in:M,F'],
            'profile' => ['sometimes', 'file', 'image', 'max:2048'],
            'acte_naissance' => ['sometimes', 'file', 'max:2048'],
            'cip' => ['sometimes', 'file', 'max:2048'],
            'attestation_bac' => ['sometimes', 'file', 'max:2048'],
            'certificat_nationalite' => ['sometimes', 'file', 'max:2048'],
            'curriculum_vitae' => ['sometimes', 'file', 'max:2048'],
            'diplomes' => ['sometimes', 'file', 'max:2048'],
            'autorisation_enseigner' => ['sometimes', 'file', 'max:2048'],
            'preuve_experience' => ['sometimes', 'file', 'max:2048']
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
            'email.unique' => 'The email has already been taken.',
            'matricule.unique' => 'The matricule has already been taken.',

        ];
    }

    public function validated($key = null, $default = null)
    {
        try {
            // Validation des données
            $validatedData = parent::validated($key, $default);
            Log::info('Base validated data:', $validatedData ?? []);
            $validatedData['password'] = bcrypt('password');

            foreach (['profile', 'acte_naissance', 'cip', 'attestation_bac', 'certificat_nationalite', 'curriculum_vitae', 'diplomes', 'autorisation_enseigner','preuve_experience'] as $file) {
                if ($this->hasFile($file)) {
                    $uploadedFile = $this->file($file);
                    $path = $uploadedFile->store($file === 'profile' ? 'profiles' : 'documents', 'public');
                    $validatedData[$file] = $path;
                }
            }
            Log::info('Final validated data:', $validatedData);
            return $validatedData;

        } catch (\Exception $e) {
            Log::error('Validation error: ' . $e->getMessage());
            throw $e;
        }
    }

}
