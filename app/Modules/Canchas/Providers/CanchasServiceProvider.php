<?php

namespace App\Modules\Canchas\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Canchas\Controllers\CanchaController;

class CanchasServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->make(CanchaController::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
