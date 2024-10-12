<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;

class ReportProgramRequest extends FormRequest
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
            'reported_id' => 'required|uuid|exists:programs,id',
            'reported_observation' => 'nullable|string|max:255',
            'reported_status' => 'required|string',
            'classe'       => 'required|uuid|exists:classes,id',
            'matiere'       => 'required|uuid|exists:matieres,id',
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
            'day' => 'required|date|after_or_equal:today',
            'h_begin' => 'required|date_format:H:i',
            'h_end' => 'required|date_format:H:i|after:h_begin',
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
            'reported_id.required' => 'L\'identifiant du programme reporté est obligatoire.',
            'reported_id.uuid' => 'L\'identifiant du programme reporté doit être un UUID valide.',
            'reported_id.exists' => 'Le programme reporté sélectionné n\'existe pas.',
            'reported_observation.string' => 'L\'observation du programme reporté doit être une chaîne de caractères.',
            'reported_observation.max' => 'L\'observation du programme reporté ne peut pas dépasser 255 caractères.',
            'reported_status.required' => 'Le statut du programme reporté est obligatoire.',
            'reported_status.string' => 'Le statut du programme reporté doit être une chaîne de caractères.',
            
            'classe.required' => 'La classe est obligatoire.',
            'classe.uuid' => 'L\'identifiant de la classe doit être un UUID valide.',
            'classe.exists' => 'La classe sélectionnée n\'existe pas.',

            'matiere.required' => 'La matière est obligatoire.',
            'matiere.uuid' => 'L\'identifiant de la matière doit être un UUID valide.',
            'matiere.exists' => 'La matière sélectionnée n\'existe pas.',

            'day.required' => 'Le jour est obligatoire.',
            'day.date' => 'La date doit être valide.',
            'day.after_or_equal' => 'Le jour doit être aujourd\'hui ou dans le futur.',

            'h_begin.required' => 'L\'heure de début est obligatoire.',
            'h_begin.date_format' => 'L\'heure de début doit être au format HH:mm.',

            'h_end.required' => 'L\'heure de fin est obligatoire.',
            'h_end.date_format' => 'L\'heure de fin doit être au format HH:mm.',
            'h_end.after' => 'L\'heure de fin doit être après l\'heure de début.',

            'observation.string' => 'L\'observation doit être une chaîne de caractères.',
            'observation.max' => 'L\'observation ne peut pas dépasser 255 caractères.',

            'report.uuid' => 'L\'identifiant du programme reporté doit être un UUID valide.',
            'report.exists' => 'Le programme reporté sélectionné n\'existe pas.',
        ];
    }
}
