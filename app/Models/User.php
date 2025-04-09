<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;
use App\Modules\Reservas\Models\Reserva;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * @method bool hasRole(array|string|\Spatie\Permission\Contracts\Role|\Spatie\Permission\Models\Role $roles, string|null $guard = null)
 * @method bool hasPermissionTo(string|array $permission, string|null $guardName = null)
 * @method bool can(string $permission, array $arguments = [])
 */
class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Authorizable;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'estado'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Obtener las reservas del usuario.
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * Obtener las reservas activas del usuario.
     */
    public function reservasActivas()
    {
        return $this->reservas()->where('estado', 'activa');
    }

    /**
     * Obtener las reservas completadas del usuario.
     */
    public function reservasCompletadas()
    {
        return $this->reservas()->where('estado', 'completada');
    }

    /**
     * Obtener las reservas canceladas del usuario.
     */
    public function reservasCanceladas()
    {
        return $this->reservas()->where('estado', 'cancelada');
    }
}
