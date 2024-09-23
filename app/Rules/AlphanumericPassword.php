<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AlphanumericPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validated = preg_match('/[A-Z]/',$value) && preg_match('/[0-9]/',$value);
        if(!$validated) {
            $fail("Le mot de passe doit contenir au moins une lettre majuscule et un chiffre");
        }
       
    }
}
