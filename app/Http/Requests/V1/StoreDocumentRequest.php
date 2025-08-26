<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\Document;

class StoreDocumentRequest extends FormRequest
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
                'message'       => 'Document Validation Errors',
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
            'name' => ['nullable', 'string', 'max:255'],
            'path' => ['nullable', 'string', 'max:500'],
            'type' => ['nullable', 'string', 'max:50'],
            'classe' => ['required', 'uuid', 'exists:classes,id'],
            'student' => ['nullable', 'uuid', 'exists:students,id'],
            'tag' => ['nullable', 'uuid', 'exists:tags,id'],
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
            'name.max' => 'Le nom du document ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Un document avec ce nom existe déjà.',
            'path.max' => 'Le chemin du document ne doit pas dépasser 500 caractères.',
            'type.max' => 'Le type de document ne doit pas dépasser 50 caractères.',
        ];
    }

}
