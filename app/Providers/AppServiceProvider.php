<?php

namespace App\Providers;

use App\Models\Medewerker\MedewerkerModel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider — applicatie-brede bootstrapping.
 *
 * Registreert route model binding en andere globale configuratie.
 */
class AppServiceProvider extends ServiceProvider
{
    /** Registreer services in de container (niet gebruikt in dit project). */
    public function register(): void
    {
        //
    }

    /** Bootstrapping na registratie: route bindings, view composers, etc. */
    public function boot(): void
    {
        // {medewerker} in URL → MedewerkerModel (404 als niet gevonden)
        Route::bind('medewerker', function (string $waarde): MedewerkerModel {
            return MedewerkerModel::query()->findOrFail($waarde);
        });

        View::share('gebruiktLegeDatabase', (bool) config('database.use_empty_database'));
        View::share('actieveDatabaseNaam', config('database.connections.mysql.database'));
    }
}
