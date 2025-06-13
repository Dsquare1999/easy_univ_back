<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Classe;

class UpdateMatiereRequest extends FormRequest
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
            'name'          => 'sometimes|string|max:255',
            'code'          => 'sometimes|string|unique:matieres,code,' . $this->route('id'). '|max:50',
            'unite'         => 'sometimes|uuid|exists:unites,id',
            'teacher'       => [
                'sometimes',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if ($user && $user->type != 1) {
                        $fail('Le chargé de l\'Unité d\'Enseignement doit être un enseignant.');
                    }
                }
            ],
            'classe'        => 'sometimes|uuid|exists:classes,id',
            'hours'         => 'sometimes|integer|min:1',
            'coefficient'   => 'sometimes|integer|min:1',
            'year_part'     => [
                'sometimes',
                'integer',
                function ($attribute, $value, $fail) {
                    $classe = Classe::find($this->classe);
                    if ($classe) {
                        if ($classe->parts === 'SEM' && ($value < 1 || $value > 2)) {
                            $fail('Les semestres ne peuvent être qu\'entre 1 et 2.');
                        }
                        if ($classe->parts === 'TRI' && ($value < 1 || $value > 3)) {
                            $fail('Les trimestres ne peuvent être qu\'entre 1 et 3.');
                        }
                    }
                },
            ],
        ];
    }


    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.max'             => 'Le nom de la matière ne doit pas dépasser 255 caractères.',
            'code.unique'          => 'Le code de la matière doit être unique.',
            'code.max'             => 'Le code ne doit pas dépasser 50 caractères.',
            'unite.exists'       => 'L\'unité d\'enseignement sélectionnée n\'existe pas.',
            'teacher.exists'       => 'L\'enseignant sélectionné n\'existe pas.',
            'classe.exists'        => 'La classe sélectionnée n\'existe pas.',
            'hours.integer'        => 'Le nombre d\'heures doit être un entier.',
            'hours.min'            => 'Le nombre d\'heures doit être au minimum 1.',
            'hours.max'            => 'Le nombre d\'heures ne peut pas dépasser 1000.',
            'coefficient.integer'  => 'Le coefficient doit être un entier.',
            'coefficient.min'      => 'Le coefficient doit être au minimum 1.',
            'coefficient.max'      => 'Le coefficient ne peut pas dépasser 10.',
            'year_part.in'         => 'La partie de l\'année doit être soit 1 soit 2.',
        ];
    }
}
