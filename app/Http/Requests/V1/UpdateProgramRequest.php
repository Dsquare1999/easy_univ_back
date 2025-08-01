<?php

namespace App\Http\Requests\V1;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;

class UpdateProgramRequest extends FormRequest
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
            'classe'       => 'sometimes|required|uuid|exists:classes,id',
            'matiere'      => 'sometimes|required|uuid|exists:matieres,id',
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
            'day' => 'sometimes|required|date|after_or_equal:today',
            'h_begin' => [
                            'sometimes',
                            'required',
                            'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/',
                        ],

            'h_end' => [
                            'sometimes',
                            'required',
                            'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/',
                            'after:h_begin',
                        ],
            'status' => 'sometimes|string|in:EFFECTUE,ANNULE,REPORTE',
            'observation' => 'nullable|string|max:255',
            'report' => 'nullable|uuid|exists:programs,id',
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

            'teacher.exists' => 'Le professeur sélectionné n\'existe pas.',
            'day.required' => 'Le jour est obligatoire.',
            'day.date' => 'La date doit être valide.',
            'day.after_or_equal' => 'Le jour doit être aujourd\'hui ou dans le futur.',

            'h_begin.required' => 'L\'heure de début est obligatoire.',

            'h_end.required' => 'L\'heure de fin est obligatoire.',
            'h_end.after' => 'L\'heure de fin doit être après l\'heure de début.',

            'observation.string' => 'L\'observation doit être une chaîne de caractères.',
            'observation.max' => 'L\'observation ne peut pas dépasser 255 caractères.',

            'report.uuid' => 'L\'identifiant du programme reporté doit être un UUID valide.',
            'report.exists' => 'Le programme reporté sélectionné n\'existe pas.',
        ];
    }
}
