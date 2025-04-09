<?php

namespace App\Modules\Canchas\Policies;

use App\Models\User;
use App\Modules\Canchas\Models\Cancha;
use Illuminate\Auth\Access\HandlesAuthorization;

class CanchaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine si el usuario puede ver el listado de canchas.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver-canchas');
    }

    /**
     * Determine si el usuario puede ver una cancha específica.
     */
    public function view(User $user, Cancha $cancha)
    {
        return $user->hasPermissionTo('ver-canchas');
    }

    /**
     * Determine si el usuario puede crear canchas.
     */
    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine si el usuario puede actualizar una cancha.
     */
    public function update(User $user, Cancha $cancha)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine si el usuario puede eliminar una cancha.
     */
    public function delete(User $user, Cancha $cancha)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine si el usuario puede ver la disponibilidad de una cancha.
     */
    public function verDisponibilidad(User $user, Cancha $cancha)
    {
        return $user->hasPermissionTo('ver-disponibilidad');
    }
}
