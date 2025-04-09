<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Canchas\Models\Cancha;
use App\Modules\Reservas\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function estadisticas()
    {
        try {
            // Estadísticas de reservas por cancha
            $reservasPorCancha = Reserva::with('cancha')
                ->selectRaw('cancha_id, count(*) as total_reservas')
                ->groupBy('cancha_id')
                ->get();

            // Estadísticas de reservas por estado
            $reservasPorEstado = Reserva::selectRaw('estado, count(*) as total')
                ->groupBy('estado')
                ->get();

            // Ingresos totales
            $ingresosTotales = Reserva::where('estado', 'activa')
                ->sum('precio_total');

            // Reservas del último mes
            $reservasUltimoMes = Reserva::where('fecha', '>=', Carbon::now()->subMonth())
                ->count();

            return response()->json([
                'reservas_por_cancha' => $reservasPorCancha,
                'reservas_por_estado' => $reservasPorEstado,
                'ingresos_totales' => $ingresosTotales,
                'reservas_ultimo_mes' => $reservasUltimoMes
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener las estadísticas'], 500);
        }
    }

    public function usuariosRegistrados(Request $request)
    {
        try {
            $query = User::with('roles');

            // Filtrar por rol si se especifica
            if ($request->has('rol')) {
                $query->role($request->rol);
            }

            // Filtrar por estado si se especifica
            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            // Búsqueda por nombre o email
            if ($request->has('buscar')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', "%{$request->buscar}%")
                      ->orWhere('email', 'like', "%{$request->buscar}%");
                });
            }

            $usuarios = $query->paginate(10);
            return response()->json($usuarios);
        } catch (\Exception $e) {
            Log::error('Error al obtener usuarios: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener los usuarios'], 500);
        }
    }

    public function resumenCanchas()
    {
        try {
            $canchas = Cancha::withCount(['reservas' => function($query) {
                $query->where('estado', 'activa');
            }])
            ->get()
            ->map(function($cancha) {
                $ingresos = $cancha->reservas()
                    ->where('estado', 'activa')
                    ->sum('precio_total');

                return [
                    'id' => $cancha->id,
                    'nombre' => $cancha->nombre,
                    'total_reservas' => $cancha->reservas_count,
                    'ingresos_totales' => $ingresos,
                    'estado' => $cancha->estado
                ];
            });

            return response()->json($canchas);
        } catch (\Exception $e) {
            Log::error('Error al obtener resumen de canchas: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener el resumen de canchas'], 500);
        }
    }

    public function reporteMensual(Request $request)
    {
        try {
            $mes = $request->get('mes', Carbon::now()->month);
            $año = $request->get('año', Carbon::now()->year);

            $reservas = Reserva::whereYear('fecha', $año)
                ->whereMonth('fecha', $mes)
                ->with(['cancha', 'user'])
                ->get()
                ->groupBy(function($reserva) {
                    return Carbon::parse($reserva->fecha)->format('Y-m-d');
                });

            $resumen = [
                'total_reservas' => $reservas->sum(function($dia) {
                    return $dia->count();
                }),
                'ingresos_totales' => $reservas->sum(function($dia) {
                    return $dia->sum('precio_total');
                }),
                'reservas_por_dia' => $reservas->map(function($dia) {
                    return [
                        'fecha' => $dia->first()->fecha,
                        'total_reservas' => $dia->count(),
                        'ingresos' => $dia->sum('precio_total')
                    ];
                })
            ];

            return response()->json($resumen);
        } catch (\Exception $e) {
            Log::error('Error al obtener reporte mensual: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener el reporte mensual'], 500);
        }
    }
}
