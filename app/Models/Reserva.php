<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'cancha_id',
        'fecha_inicio',
        'fecha_fin',
        'duracion_horas',
        'precio_total',
        'estado',
        'notas'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'duracion_horas' => 'integer',
        'precio_total' => 'decimal:2'
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con cancha
    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    // Método para calcular precio total
    public function calcularPrecioTotal()
    {
        return $this->duracion_horas * $this->cancha->precio_hora;
    }

    // Scope para reservas activas
    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['pendiente', 'confirmada']);
    }

    // Scope para reservas del usuario
    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
