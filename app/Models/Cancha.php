<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cancha extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad',
        'precio_hora',
        'estado',
        'descripcion'
    ];

    protected $casts = [
        'precio_hora' => 'decimal:2',
        'capacidad' => 'integer'
    ];

    // Relación con reservas
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    // Método para verificar disponibilidad
    public function verificarDisponibilidad($fecha_inicio, $fecha_fin)
    {
        return !$this->reservas()
            ->where('estado', '!=', 'cancelada')
            ->where(function ($query) use ($fecha_inicio, $fecha_fin) {
                $query->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_fin])
                    ->orWhereBetween('fecha_fin', [$fecha_inicio, $fecha_fin])
                    ->orWhere(function ($q) use ($fecha_inicio, $fecha_fin) {
                        $q->where('fecha_inicio', '<=', $fecha_inicio)
                            ->where('fecha_fin', '>=', $fecha_fin);
                    });
            })
            ->exists();
    }
}
