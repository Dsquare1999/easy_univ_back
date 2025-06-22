<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\Cycle;

class UpdateCycleRequest extends FormRequest
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
            'message' => 'Cycle Validation Errors',
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
        
        $method = $this->method();
        
        if($method == 'PUT'){
            return [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'duration' => ['required', 'integer', 'min:1'],
            ];
        }else{
            return [
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'description' => ['sometimes', 'nullable', 'string'],
                'duration' => ['sometimes', 'required', 'integer', 'min:1'],
            ];
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du cycle est requis.',
            'name.string'   => 'Le nom du cycle doit être une chaîne de caractères.',
            'name.max'      => 'Le nom du cycle ne doit pas dépasser 255 caractères.',
            'duration.required' => 'La durée du cycle est requise.',
            'duration.integer'  => 'La durée du cycle doit être un entier.',
            'duration.min'      => 'La durée du cycle doit être d\'au moins 1.',
        ];
    }

    /**
     * Add validation for the slug.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        $slug = Str::slug($data['name']);

        if (Cycle::where('slug', $slug)->where('id', '!=', $this->route('id'))->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'Le Cycle avec ce nom existe déjà.'
            ]);
        }

        $data['slug'] = $slug;

        return $data;
    }
}
