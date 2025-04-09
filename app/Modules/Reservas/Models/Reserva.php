<?php

namespace App\Modules\Reservas\Models;

use App\Modules\Canchas\Models\Cancha;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Reserva extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cancha_id',
        'user_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado', // activa, cancelada, completada
        'precio_total',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'precio_total' => 'decimal:2',
    ];

    // Relaciones
    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeProximas($query)
    {
        return $query->where('fecha', '>=', now()->format('Y-m-d'))
                    ->where('estado', 'activa')
                    ->orderBy('fecha')
                    ->orderBy('hora_inicio');
    }

    // Métodos
    public function calcularPrecioTotal()
    {
        $horaInicio = \Carbon\Carbon::parse($this->hora_inicio);
        $horaFin = \Carbon\Carbon::parse($this->hora_fin);
        $duracionHoras = $horaFin->diffInHours($horaInicio);

        return $this->cancha->precio_hora * $duracionHoras;
    }

    public function puedeSerEditada()
    {
        return $this->estado === 'activa' &&
               \Carbon\Carbon::parse($this->fecha . ' ' . $this->hora_inicio)
                   ->isAfter(now());
    }

    public function puedeSerCancelada()
    {
        return $this->estado === 'activa' &&
               \Carbon\Carbon::parse($this->fecha . ' ' . $this->hora_inicio)
                   ->isAfter(now()->addHours(2)); // 2 horas mínimo para cancelar
    }
}
