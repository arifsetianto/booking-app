<?php

namespace App\Providers;

use App\Rules\NoHyphen;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('emails', function($job) {
            return Limit::perMinute(120);
        });

        Validator::extend('no_hyphen', function ($attribute, $value, $parameters, $validator) {
            return !str_contains($value, '-');
        });

        Validator::replacer('no_hyphen', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute cannot contain a hyphen.');
        });
    }
}
