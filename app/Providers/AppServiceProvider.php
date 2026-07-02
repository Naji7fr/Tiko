<?php

namespace App\Providers;

use App\Models\Medewerker\MedewerkerModel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Route model binding: medewerker → MedewerkerModel (MVC)
        Route::bind('medewerker', function (string $waarde): MedewerkerModel {
            return MedewerkerModel::query()->findOrFail($waarde);
        });
    }
}
