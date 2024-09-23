<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StripHtmlTags implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cleanedValue = strip_tags($value);

        if ($cleanedValue !== $value) {
            $fail('The :attribute field must not contain HTML tags.');
        }
    }
}
