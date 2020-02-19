<?php

namespace App\Providers;

use App\Validator\CustomValidator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        Validator::extend('alpha_num_check', function($attribute, $value, $parameters, $validator) {
            return preg_match('/^[A-Za-z\d]+$/', $value);
        });
    }
}
