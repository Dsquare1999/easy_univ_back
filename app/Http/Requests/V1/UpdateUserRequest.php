<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        Log::info('Content-Type: ' . $this->header('Content-Type'));
        Log::info('Method: ' . $this->method());
        Log::info('Request Data:', $this->all());
        Log::info('Route Parameters:', $this->route()->parameters());
        return true;
    }

    protected function prepareForValidation()
    {
        Log::info('PrepareForValidation called');
        
        // Log all possible sources of input
        Log::info('Raw input methods:', [
            'all()' => $this->all(),
            'input()' => $this->input(),
            'request->all()' => $this->request->all(),
            'query()' => $this->query(),
            'post()' => $_POST,
            'files' => $_FILES,
        ]);
        
        // Pour les fichiers
        $files = $this->allFiles();
        Log::info('Files received:', array_keys($files));
        
        // Pour les autres données
        $input = $this->all();
        Log::info('All input data:', $input);
        
        // Si les données sont dans le corps de la requête mais pas dans input()
        if (empty($input)) {
            $rawContent = $this->getContent();
            Log::info('Raw request content:', ['content' => $rawContent]);
            
            // Essayez de parser le contenu si c'est du JSON
            if ($this->isJson()) {
                $jsonData = json_decode($rawContent, true);
                Log::info('Parsed JSON data:', $jsonData ?? []);
                foreach ($jsonData as $key => $value) {
                    $this->merge([$key => $value]);
                }
            }
        }
    }

    public function rules(): array
    {
        $userId = $this->route('id');
        Log::info('User ID from route: ' . $userId);

        return [
            'firstname' => ['sometimes', 'string', 'max:255'],
            'lastname' => ['sometimes', 'string', 'max:255'],
            'matricule' => ['sometimes', 'string', 'max:50', 'unique:users,matricule,' . $userId],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $userId],
            'nationality' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'type' => ['sometimes', 'string'],
            'birthdate' => ['sometimes', 'nullable', 'date'],
            'birthplace' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sexe' => ['sometimes', 'nullable', 'string', 'in:M,F'],
            'profile' => ['sometimes', 'nullable', 'file', 'max:2048'],
            'acte_naissance' => ['sometimes', 'nullable', 'file', 'max:2048'],
            'cip' => ['sometimes', 'nullable', 'file', 'max:2048'],
            'attestation_bac' => ['sometimes', 'nullable', 'file', 'max:2048'],
            'certificat_nationalite' => ['sometimes', 'nullable', 'file', 'max:2048'],
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
        Log::info('Validated method called');
        try {
            $validatedData = parent::validated($key, $default);
            Log::info('Base validated data:', $validatedData ?? []);
            $validatedData['password'] = bcrypt('password');

            // Traitement des fichiers
            foreach (['profile', 'acte_naissance', 'cip', 'attestation_bac', 'certificat_nationalite', 'curriculum_vitae', 'diplomes', 'autorisation_enseigner','preuve_experience'] as $file) {
                if ($this->hasFile($file)) {
                    $uploadedFile = $this->file($file);
                    $path = $uploadedFile->store($file === 'profile' ? 'profiles' : 'documents', 'public');
                    $validatedData[$file] = $path;
                }
            }
            // Log final des données validées
            Log::info('Final validated data:', $validatedData);
            return $validatedData;

        } catch (\Exception $e) {
            Log::error('Validation error: ' . $e->getMessage());
            throw $e;
        }
    }
}