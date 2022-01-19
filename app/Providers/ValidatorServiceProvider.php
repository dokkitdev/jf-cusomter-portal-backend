<?php

namespace App\Providers;

use Illuminate\Validation\Factory;
use Illuminate\Support\ServiceProvider;
use App\Support\Validators\ExtendedValidator;

class ValidatorServiceProvider extends ServiceProvider {

    public function boot(Factory $validator)
    {
        $validator->resolver(function($translator, $data, $rules, $messages) {
            return new ExtendedValidator($translator, $data, $rules, $messages);
        });
    }
}