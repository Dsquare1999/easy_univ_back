<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use App\Models\Invoice;

class StoreInvoiceRequest extends FormRequest
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
            'message' => 'Invoice validation errors',
            'errors'  => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'number'        => 'required|string|max:255|unique:invoices,number',
            'amount'        => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'user_id'       => 'required|exists:users,id',
            'tag_id'        => 'required|exists:tags,id',
            'classe_id'     => 'required|exists:classes,id',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'number.required'       => 'Invoice number is required.',
            'number.unique'         => 'Invoice number must be unique.',
            'amount.required'       => 'Amount is required.',
            'amount.numeric'        => 'Amount must be a number.',
            'user.required'      => 'The user is required.',
            'user.exists'        => 'This user is not valid.',
            'tag.required'       => 'The tag is required.',
            'tag.exists'         => 'This tag is not valid.',
            'classe.required'    => 'The classe is required.',
            'classe.exists'      => 'This classe is not valid.',
        ];
    }

    /**
     * Add validation for the slug.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (Invoice::where('number', $data['number'])->exists()) {
            throw ValidationException::withMessages([
                'number' => 'Cette Facture existe déjà.'
            ]);
        }

        return $data;
    }
}
