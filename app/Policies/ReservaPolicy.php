<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Reservas\Models\Reserva;

class ReservaPolicy
{
    public function view(User $user, Reserva $reserva)
    {
        return $user->id === $reserva->user_id || $user->hasRole('admin');
    }

    public function update(User $user, Reserva $reserva)
    {
        return $user->id === $reserva->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, Reserva $reserva)
    {
        return $user->id === $reserva->user_id || $user->hasRole('admin');
    }
}
