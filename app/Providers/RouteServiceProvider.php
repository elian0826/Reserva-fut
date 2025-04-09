<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Modules\RolesPermisos\Models\Role;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        parent::boot();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // Binding explícito para el modelo Role
        Route::bind('role', function ($value) {
            return Role::findOrFail($value);
        });

        // Cargar rutas de módulos
        $this->loadModuleRoutes();
    }

    protected function loadModuleRoutes(): void
    {
        // Cargar rutas del módulo de autenticación
        $authRoutesPath = app_path('Modules/Auth/Routes/api.php');
        if (file_exists($authRoutesPath)) {
            Route::middleware('api')->group($authRoutesPath);
        }

        // Cargar rutas del módulo de canchas
        $canchasRoutesPath = app_path('Modules/Canchas/Routes/api.php');
        if (file_exists($canchasRoutesPath)) {
            Route::middleware('api')->group($canchasRoutesPath);
        }

        // Cargar rutas del módulo de reservas
        $reservasRoutesPath = app_path('Modules/Reservas/Routes/api.php');
        if (file_exists($reservasRoutesPath)) {
            Route::middleware('api')->group($reservasRoutesPath);
        }

        // Cargar rutas del módulo de dashboard
        $dashboardRoutesPath = app_path('Modules/Dashboard/Routes/api.php');
        if (file_exists($dashboardRoutesPath)) {
            Route::middleware('api')->group($dashboardRoutesPath);
        }

        // Cargar rutas del módulo de roles y permisos
        $rolesPermisosPath = app_path('Modules/RolesPermisos/Routes/api.php');
        if (file_exists($rolesPermisosPath)) {
            Route::middleware('api')->group($rolesPermisosPath);
        }
    }
}

