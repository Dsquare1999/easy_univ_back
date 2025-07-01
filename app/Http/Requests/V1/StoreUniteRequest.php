<?php

namespace App\Http\Requests\V1;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\Unite;

class StoreUniteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
                'success'       => false,
                'message'       => 'Unit Validation Errors',
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
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
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
            '*.required' => 'Ce champ est obligatoire.',
            '*.string' => 'Ce champ doit être une chaîne de caractères.',
            'name.max' => 'Le nom de l\'unité d\'enseignement ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Une unité d\'enseignement avec ce nom existe déjà.',
            'description.max' => 'La description ne doit pas dépasser 500 caractères.',
        ];
    }

    /**
     * Add validation for the slug.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        $slug = \Illuminate\Support\Str::slug($data['name']);

        if (Unite::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'Une unité d\'enseignement avec ce nom existe déjà.'
            ]);
        }

        $data['slug'] = $slug;

        return $data;
    }
}
