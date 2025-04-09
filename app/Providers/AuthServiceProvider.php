<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Modules\Reservas\Models\Reserva;
use App\Modules\Reservas\Policies\ReservaPolicy;
use App\Modules\Canchas\Models\Cancha;
use App\Modules\Canchas\Policies\CanchaPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Reserva::class => ReservaPolicy::class,
        Cancha::class => CanchaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Establecer el guard por defecto
        Auth::shouldUse('api');
    }
}
