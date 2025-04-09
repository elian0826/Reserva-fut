<?php

namespace App\Modules\Auth\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Modules\Canchas\Models\Cancha;
use App\Modules\Canchas\Policies\CanchaPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Cancha::class => CanchaPolicy::class,
    ];

    public function register()
    {
        // Registrar el módulo
    }

    public function boot()
    {
        $this->registerPolicies();
        // Cargar rutas
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
    }
}
