<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Cycle;

class UpdateClasseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Classe validation errors',
            'errors'  => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        $method = $this->method();

        $rules = [
            'filiere'        => ['required', 'exists:filieres,id'],
            'cycle'          => ['required', 'exists:cycles,id'],
            'year'           => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    $cycle = Cycle::find($this->input('cycle'));
                    if ($cycle && $value > $cycle->duration) {
                        $fail("The year must be between 1 and {$cycle->duration}.");
                    }
                }
            ],
            'parts'          => ['required', 'in:SEM,TRI'],
            'academic_year'  => ['required'],
        ];

        if ($method !== 'PUT') {
            foreach ($rules as $key => &$rule) {
                $rule = array_merge(['sometimes'], (array)$rule);
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'filiere.required'       => 'Le champ filière est requis.',
            'filiere.exists'         => 'Le champ filière est invalide.',
            'cycle.required'         => 'Le champ cycle est requis.',
            'cycle.exists'           => 'Le champ cycle est invalide.',
            'year.required'          => 'Le champ année est requis.',
            'year.numeric'           => 'L\'année doit être un entier.',
            'year.min'               => 'L\'année doit être au moins 1.',
            'parts.required'         => 'Le champ parties est requis.',
            'parts.in'               => 'Le champ parties doit être soit SEM, soit TRI.',
            'academic_year.required' => 'Le champ année académique est requis.'
        ];
    }
}
