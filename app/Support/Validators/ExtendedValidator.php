<?php

namespace App\Support\Validators;

use Illuminate\Validation\Validator;

class ExtendedValidator extends Validator
{
    public function validateNewPassword($attribute, $value, $parameters)
    {
        return (strlen($value) >= 8) && (preg_match('/\d+/', $value)) && (preg_match('/\D+/', $value));
    }
}
