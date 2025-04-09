<?php

namespace App\Modules\Canchas\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Reservas\Models\Reserva;
use Carbon\Carbon;

class Cancha extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'canchas';

    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad',
        'precio_hora',
        'estado',
        'descripcion'
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'precio_hora' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = ['deleted_at'];

    // Relación con reservas
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    // Scope para canchas disponibles
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    // Scope para filtrar canchas disponibles por fecha y hora
    public function scopeDisponiblesPara($query, $fecha, $horaInicio, $horaFin)
    {
        return $query->where('estado', 'disponible')
            ->whereDoesntHave('reservas', function ($q) use ($fecha, $horaInicio, $horaFin) {
                $q->where('fecha', $fecha)
                  ->where('estado', 'activa')
                  ->where(function ($subq) use ($horaInicio, $horaFin) {
                      $subq->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                           ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                           ->orWhere(function ($innerq) use ($horaInicio, $horaFin) {
                               $innerq->where('hora_inicio', '<=', $horaInicio)
                                     ->where('hora_fin', '>=', $horaFin);
                           });
                  });
            });
    }

    // Verificar disponibilidad específica
    public function estaDisponible($fecha, $horaInicio, $horaFin)
    {
        if ($this->estado !== 'disponible') {
            return false;
        }

        return !$this->reservas()
            ->where('fecha', $fecha)
            ->where('estado', 'activa')
            ->where(function ($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                      ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                      ->orWhere(function ($q) use ($horaInicio, $horaFin) {
                          $q->where('hora_inicio', '<=', $horaInicio)
                            ->where('hora_fin', '>=', $horaFin);
                      });
            })->exists();
    }

    // Obtener próximas reservas
    public function proximasReservas($limite = 5)
    {
        return $this->reservas()
            ->where('fecha', '>=', Carbon::today())
            ->where('estado', 'activa')
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->limit($limite)
            ->get();
    }
}
