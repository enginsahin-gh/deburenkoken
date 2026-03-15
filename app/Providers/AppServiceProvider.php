<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
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
        // Only force HTTPS in production and test environments, not in local/CI testing
        if (app()->environment(['production', 'test', 'local']) && ! app()->environment('testing')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        Validator::extend('valid_time_and_min_time', function ($attribute, $value, $parameters, $validator) {
            $lowerBound = strtotime($parameters[0]); // Tijdwaarde voor ondergrens
            $upperBound = strtotime($parameters[1]); // Tijdwaarde voor bovengrens

            // Convert de opgegeven tijd naar Unix timestamp
            $currentValue = strtotime($value);

            // Controleer of de opgegeven tijd binnen het bereik ligt
            if ($currentValue < $lowerBound || $currentValue > $upperBound) {
                $validator->errors()->add($attribute, 'De verwachte aankomsttijd moet in de ophaal tijden zitten');
            }

            return true; // Altijd true retourneren omdat we de foutmeldingen handmatig toevoegen
        });
    }
}
