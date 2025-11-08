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
            'birthdate' => ['nullable', 'date'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'sexe'    => ['nullable', 'string', 'in:M,F'],
            'acte_naissance' => ['sometimes', 'file', 'max:2048'],
            'cip' => ['sometimes', 'file', 'max:2048'],
            'attestation_bac' => ['sometimes', 'file', 'max:2048'],
            'certificat_nationalite' => ['sometimes', 'file', 'max:2048'],
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

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        
        if ($this->hasFile('profile')) {
            $file = $this->file('profile');
            $path = $file->store('profiles', 'public');
            $data['profile'] = $path;
        }
        if ($this->hasFile('acte_naissance')) {
            $acte_naissance = $this->file('acte_naissance');
            $path = $acte_naissance->store('documents', 'public');
            $data['acte_naissance'] = $path;
        }
        if ($this->hasFile('cip')) {
            $cip = $this->file('cip');
            $path = $cip->store('documents', 'public');
            $data['cip'] = $path;
        }
        if ($this->hasFile('attestation_bac')) {
            $attestation_bac = $this->file('attestation_bac');
            $path = $attestation_bac->store('documents', 'public');
            $data['attestation_bac'] = $path;
        }
        if ($this->hasFile('certificat_nationalite')) {
            $certificat_nationalite = $this->file('certificat_nationalite');
            $path = $certificat_nationalite->store('documents', 'public');
            $data['certificat_nationalite'] = $path;
        }
        
        return $data;
    }
}

