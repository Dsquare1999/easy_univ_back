<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\Archive;

class StoreArchiveRequest extends FormRequest
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
            'message' => 'Archive Validation Errors',
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'year' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
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
            'name.required' => 'Le nom du document est requis.',
            'name.string'   => 'Le nom du document doit être une chaîne de caractères.',
            'name.max'      => 'Le nom du document ne doit pas dépasser 255 caractères.',
            'year.required' => 'L\'année est requise.',
            'year.string'   => 'L\'année doit être une chaîne de caractères.',
            'file.required' => 'Le fichier est requis.',
            'file.file'     => 'Le fichier doit être un fichier valide.',
            'file.mimes'    => 'Le fichier doit être au format: pdf, doc, docx, xls, xlsx, jpg, jpeg, png.',
            'file.max'      => 'Le fichier ne doit pas dépasser 10 Mo.',
        ];
    }

    /**
     * Add validation for the slug.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        $slug = \Illuminate\Support\Str::slug($data['name']);

        if (Archive::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'L\'Archive avec ce nom existe déjà.'
            ]);
        }

        $data['slug'] = $slug;

        return $data;
    }

}
