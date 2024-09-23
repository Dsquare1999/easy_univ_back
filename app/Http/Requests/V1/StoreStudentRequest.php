<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\Student;

class StoreStudentRequest extends FormRequest
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
            'message' => 'Student validation errors',
            'errors'  => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'classe'     => 'required|exists:classes,id',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'classe.required'    => 'The classe is required.',
            'classe.exists'      => 'This classe is not valid.',
        ];
    }
}
