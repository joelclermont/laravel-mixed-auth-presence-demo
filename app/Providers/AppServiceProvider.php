<?php

namespace App\Providers;

use App\Rules\Phone;
use App\Rules\PhoneNew;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\InvokableValidationRule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['validator']->extend('phone', Phone::class . '@passes');

        $this->app['validator']->extend('phone_new', fn($attribute, $value, $parameters, $validator) => InvokableValidationRule::make(new PhoneNew())->setValidator($validator)->passes($attribute, $value));
    }
}
