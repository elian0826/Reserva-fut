<?php

namespace App\Modules\Reservas\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reservas\Models\Reserva;
use App\Modules\Canchas\Models\Cancha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReservaController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        try {
            $query = Reserva::with(['cancha', 'user']);

            // Si no es admin, solo mostrar sus reservas
            if (!$request->user()->hasRole('admin')) {
                $query->where('user_id', $request->user()->id);
            }

            // Filtros
            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('fecha')) {
                $query->whereDate('fecha', $request->fecha);
            }

            $reservas = $query->get();
            return response()->json($reservas);
        } catch (\Exception $e) {
            Log::error('Error al obtener reservas: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener las reservas'], 500);
        }
    }

    public function misReservas(Request $request)
    {
        try {
            $query = $request->user()->reservas()->with('cancha');

            // Filtros
            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('fecha')) {
                $query->whereDate('fecha', $request->fecha);
            }

            // Ordenar por fecha y hora
            $query->orderBy('fecha', 'desc')
                  ->orderBy('hora_inicio', 'desc');

            $reservas = $query->get();
            return response()->json($reservas);
        } catch (\Exception $e) {
            Log::error('Error al obtener reservas del usuario: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener tus reservas'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cancha_id' => 'required|exists:canchas,id',
                'fecha' => 'required|date_format:Y-m-d|after_or_equal:today',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Verificar disponibilidad
            $cancha = Cancha::findOrFail($request->cancha_id);
            $fecha = Carbon::parse($request->fecha);
            $horaInicio = Carbon::parse($request->hora_inicio);
            $horaFin = Carbon::parse($request->hora_fin);

            // Validar horario de operación (6:00 AM a 10:00 PM)
            if ($horaInicio->format('H:i') < '06:00' || $horaFin->format('H:i') > '22:00') {
                return response()->json([
                    'message' => 'El horario de reservas es de 6:00 AM a 10:00 PM'
                ], 422);
            }

            $reservaExistente = Reserva::where('cancha_id', $cancha->id)
                ->whereDate('fecha', $fecha)
                ->where('estado', 'activa')
                ->where(function ($query) use ($horaInicio, $horaFin) {
                    $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                        ->orWhereBetween('hora_fin', [$horaInicio, $horaFin]);
                })
                ->exists();

            if ($reservaExistente) {
                return response()->json([
                    'message' => 'La cancha no está disponible en ese horario'
                ], 422);
            }

            // Calcular precio total
            $duracion = $horaFin->diffInHours($horaInicio);
            if ($duracion < 1) {
                return response()->json([
                    'message' => 'La reserva debe ser de al menos 1 hora'
                ], 422);
            }

            $precioTotal = $cancha->precio_hora * $duracion;

            $reserva = Reserva::create([
                'cancha_id' => $request->cancha_id,
                'user_id' => $request->user()->id,
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'precio_total' => $precioTotal,
                'estado' => 'activa'
            ]);

            return response()->json($reserva, 201);
        } catch (\Exception $e) {
            Log::error('Error al crear reserva: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear la reserva'], 500);
        }
    }

    public function show(Reserva $reserva)
    {
        try {
            $this->authorize('view', $reserva);
            return response()->json($reserva->load(['cancha', 'user']));
        } catch (\Exception $e) {
            Log::error('Error al obtener reserva: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener la reserva'], 500);
        }
    }

    public function update(Request $request, Reserva $reserva)
    {
        try {
            $this->authorize('update', $reserva);

            $validator = Validator::make($request->all(), [
                'fecha' => 'required|date_format:Y-m-d|after_or_equal:today',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Validar horario de operación (6:00 AM a 10:00 PM)
            $horaInicio = Carbon::parse($request->hora_inicio);
            $horaFin = Carbon::parse($request->hora_fin);
            if ($horaInicio->format('H:i') < '06:00' || $horaFin->format('H:i') > '22:00') {
                return response()->json([
                    'message' => 'El horario de reservas es de 6:00 AM a 10:00 PM'
                ], 422);
            }

            // Verificar disponibilidad (excluyendo la reserva actual)
            $fecha = Carbon::parse($request->fecha);
            $reservaExistente = Reserva::where('cancha_id', $reserva->cancha_id)
                ->where('id', '!=', $reserva->id)
                ->whereDate('fecha', $fecha)
                ->where('estado', 'activa')
                ->where(function ($query) use ($horaInicio, $horaFin) {
                    $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                        ->orWhereBetween('hora_fin', [$horaInicio, $horaFin]);
                })
                ->exists();

            if ($reservaExistente) {
                return response()->json([
                    'message' => 'La cancha no está disponible en ese horario'
                ], 422);
            }

            // Validar duración mínima
            $duracion = $horaFin->diffInHours($horaInicio);
            if ($duracion < 1) {
                return response()->json([
                    'message' => 'La reserva debe ser de al menos 1 hora'
                ], 422);
            }

            // Calcular nuevo precio total
            $precioTotal = $reserva->cancha->precio_hora * $duracion;

            $reserva->update([
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'precio_total' => $precioTotal
            ]);

            return response()->json($reserva);
        } catch (\Exception $e) {
            Log::error('Error al actualizar reserva: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar la reserva'], 500);
        }
    }

    public function destroy(Reserva $reserva)
    {
        try {
            $this->authorize('delete', $reserva);

            if (!$reserva->puedeSerCancelada()) {
                return response()->json([
                    'message' => 'La reserva no puede ser cancelada con menos de 2 horas de anticipación'
                ], 422);
            }

            $reserva->update(['estado' => 'cancelada']);
            return response()->json(['message' => 'Reserva cancelada exitosamente']);
        } catch (\Exception $e) {
            Log::error('Error al cancelar reserva: ' . $e->getMessage());
            return response()->json(['message' => 'Error al cancelar la reserva'], 500);
        }
    }
}
