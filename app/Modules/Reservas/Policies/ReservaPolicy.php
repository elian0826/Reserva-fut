<?php

namespace App\Modules\Reservas\Policies;

use App\Models\User;
use App\Modules\Reservas\Models\Reserva;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reserva $reserva): bool
    {
        return $user->id === $reserva->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reserva $reserva): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->id !== $reserva->user_id) {
            return false;
        }

        return $reserva->puedeSerEditada();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reserva $reserva): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->id !== $reserva->user_id) {
            return false;
        }

        return $reserva->puedeSerCancelada();
    }
} 