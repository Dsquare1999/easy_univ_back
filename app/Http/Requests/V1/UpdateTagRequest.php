<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\Tag;

class UpdateTagRequest extends FormRequest
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
                'message'       => 'Tag Validation Errors',
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
        $method = $this->method();
        
        if($method == 'PUT'){
            return [
                'name' => ['required', 'string', 'max:255'],
                'fee' => ['required', 'numeric'],
            ];
        }else{
            return [
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'fee' => ['sometimes', 'required', 'numeric'],
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
            '*.string'              => 'Le Tag doit être une chaîne de caractère.',
            '*.required'            => 'Le nom du Tag est requis.',
            'name.max'              => 'Le nom du Tag ne doit pas dépasser 255 caractères.',
            'fee.required'          => 'Le montant du Tag est requis.',
        ];
    }

    /**
     * Add validation for the slug.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        $slug = Str::slug($data['name']);

        if (Tag::where('slug', $slug)->where('id', '!=', $this->route('id'))->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'Le Tag avec ce nom existe déjà.'
            ]);
        }

        $data['slug'] = $slug;

        return $data;
    }
}
