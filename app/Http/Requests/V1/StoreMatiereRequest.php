<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;
use App\Models\Classe;
use App\Models\Unite;

class StoreMatiereRequest extends FormRequest
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
            'name'         => 'required|string|max:255',
            'code'         => 'required|string|unique:matieres,code|max:50',
            'unite'        => 'required|uuid|exists:unites,id',
            'teacher'      => [
                'sometimes',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if ($user && $user->type != 1) {
                        $fail('Le chargé de l\'Unité d\'Enseignement doit être un enseignant.');
                    }
                }
            ],
            'classe'       => 'required|uuid|exists:classes,id',
            'hours'        => 'required|integer|min:1',
            'coefficient'  => 'required|integer|min:1',
            'year_part'    => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $classe = Classe::find($this->classe);
                    if ($classe) {
                        if ($classe->parts === 'SEM' && ($value < 1 || $value > 2)) {
                            $fail('Pour des semestres, year_part doit être entre 1 et 2.');
                        }
                        if ($classe->parts === 'TRI' && ($value < 1 || $value > 3)) {
                            $fail('Pour des trimestres, year_part doit être entre 1 et 3.');
                        }
                    }
                },
            ],
        ];
    }


    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();
        $classe = Classe::find($validated['classe']);
        if ($classe) {
            $matieres_this_part_count = $classe->matieres()
            ->where('year_part', $validated['year_part'])
            ->count();
            $libelle = 2 * $matieres_this_part_count + $validated['year_part'];
            $libelle_formatted = str_pad($libelle, 2, '0', STR_PAD_LEFT);
            $validated['libelle'] = $validated['code'] . ' ' . $classe->year . $libelle_formatted;
        }

        return $validated;
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'        => 'Le nom de la matière est obligatoire.',
            'name.max'             => 'Le nom de la matière ne doit pas dépasser 255 caractères.',
            'unite.required'        => 'L\'unité d\'enseignement de la matière est obligatoire.',
            'code.required'        => 'Le code de la matière est obligatoire.',
            'code.unique'          => 'Le code de la matière doit être unique.',
            'code.max'             => 'Le code ne doit pas dépasser 50 caractères.',
            'teacher.required'     => 'Un enseignant est obligatoire.',
            'teacher.exists'       => 'L\'enseignant sélectionné n\'existe pas.',
            'classe.required'      => 'Une classe est obligatoire.',
            'classe.exists'        => 'La classe sélectionnée n\'existe pas.',
            'hours.required'       => 'Le nombre d\'heures est obligatoire.',
            'hours.integer'        => 'Le nombre d\'heures doit être un entier.',
            'hours.min'            => 'Le nombre d\'heures doit être au minimum 1.',
            'hours.max'            => 'Le nombre d\'heures ne peut pas dépasser 1000.',
            'coefficient.required' => 'Le coefficient est obligatoire.',
            'coefficient.integer'  => 'Le coefficient doit être un entier.',
            'coefficient.min'      => 'Le coefficient doit être au minimum 1.',
            'coefficient.max'      => 'Le coefficient ne peut pas dépasser 10.',
            'year_part.required'   => 'La partie de l\'année est obligatoire.',
            'year_part.integer'    => 'La partie de l\'année doit être un entier.',
        ];
    }
}
