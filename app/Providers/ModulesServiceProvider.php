<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar los módulos aquí
    }

    public function boot()
    {
        // Cargar rutas de los módulos
        $this->loadRoutesFrom(__DIR__.'/../Modules/Auth/Routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../Modules/RolesPermisos/Routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../Modules/Canchas/Routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../Modules/Reservas/Routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../Modules/Dashboard/Routes/api.php');
    }
}
