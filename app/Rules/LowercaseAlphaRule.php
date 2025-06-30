<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class LowercaseAlphaRule implements ValidationRule
{
    /**
     * Validate the attribute.
     *
     * @param  Closure(string, string|null=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || $value !== strtolower($value) || ! preg_match('/^[a-z]+$/', $value)) {
            $fail('The :attribute must contain only lowercase letters.');
        }
    }
}
