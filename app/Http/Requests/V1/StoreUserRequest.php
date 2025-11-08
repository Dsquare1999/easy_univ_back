<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
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
            'birthdate' => ['nullable', 'date'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'sexe'    => ['nullable', 'string', 'in:M,F'],
            'profile' => ['sometimes', 'file', 'image', 'max:2048'],
            'acte_naissance' => ['sometimes', 'file', 'max:2048'],
            'cip' => ['sometimes', 'file', 'max:2048'],
            'attestation_bac' => ['sometimes', 'file', 'max:2048'],
            'certificat_nationalite' => ['sometimes', 'file', 'max:2048'],
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
        $data = parent::validated($key, $default);
        $data['password'] = 'password123'; 
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
