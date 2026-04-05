<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\Archive;
use Illuminate\Validation\ValidationException;

class UpdateArchiveRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'year' => ['sometimes', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
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
            'name.string'   => 'Le nom du document doit être une chaîne de caractères.',
            'name.max'      => 'Le nom du document ne doit pas dépasser 255 caractères.',
            'year.string'   => 'L\'année doit être une chaîne de caractères.',
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
        
        if (isset($data['name'])) {
            $slug = \Illuminate\Support\Str::slug($data['name']);

            // Vérifier si le slug existe déjà (sauf pour l'archive actuelle)
            $archiveId = $this->route('id');
            if (Archive::where('slug', $slug)->where('id', '!=', $archiveId)->exists()) {
                throw ValidationException::withMessages([
                    'slug' => 'L\'Archive avec ce nom existe déjà.'
                ]);
            }

            $data['slug'] = $slug;
        }

        return $data;
    }

}
