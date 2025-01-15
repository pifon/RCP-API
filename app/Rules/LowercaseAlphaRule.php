<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LowercaseAlphaRule implements ValidationRule
{
    /**
     * Validate the attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): void  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || $value !== strtolower($value) || !preg_match('/^[a-z]+$/', $value)) {
            $fail(trans('cuisine.filter.lowercase'));
        }
    }
}