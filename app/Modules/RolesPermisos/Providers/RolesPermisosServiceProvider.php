<?php

namespace App\Modules\RolesPermisos\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\RolesPermisos\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesPermisosServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar el binding del modelo Role
        $this->app->bind('role', function ($app) {
            return new Role();
        });
    }

    public function boot()
    {
        // Cargar rutas
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        
        // Limpiar caché de permisos
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
} 