<?php

namespace App\Modules\Canchas\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Canchas\Models\Cancha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Modules\Canchas\Requests\CanchaRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class CanchaController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:admin')->only(['store', 'update', 'destroy']);
    }

    /**
     * Mostrar listado de todas las canchas con paginación
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $canchas = Cancha::with(['reservas' => function($query) {
                $query->where('estado', 'activa');
            }])->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $canchas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener canchas: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener las canchas'
            ], 500);
        }
    }

    /**
     * Mostrar canchas disponibles con filtros
     * @return \Illuminate\Http\JsonResponse
     */
    public function disponibles(Request $request)
    {
        try {
            $query = Cancha::disponibles();

            // Filtro por capacidad mínima
            if ($request->has('capacidad_minima')) {
                $query->where('capacidad', '>=', $request->capacidad_minima);
            }

            // Filtro por precio máximo
            if ($request->has('precio_maximo')) {
                $query->where('precio_hora', '<=', $request->precio_maximo);
            }

            $canchas = $query->select(['id', 'nombre', 'ubicacion', 'capacidad', 'precio_hora', 'descripcion'])
                           ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'canchas' => $canchas,
                    'total' => $canchas->count(),
                    'filtros_aplicados' => [
                        'capacidad_minima' => $request->capacidad_minima ?? 'no aplicado',
                        'precio_maximo' => $request->precio_maximo ?? 'no aplicado'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener canchas disponibles: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener las canchas disponibles'
            ], 500);
        }
    }

    /**
     * Mostrar una cancha específica con sus reservas activas
     * @param Cancha $cancha
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Cancha $cancha)
    {
        try {
            $cancha->load(['reservas' => function($query) {
                $query->where('estado', 'activa')
                      ->orderBy('fecha', 'asc')
                      ->orderBy('hora_inicio', 'asc');
            }]);

            return response()->json([
                'status' => 'success',
                'data' => $cancha
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener la cancha: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener la cancha'
            ], 500);
        }
    }

    /**
     * Crear una nueva cancha (solo admin)
     * @param CanchaRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CanchaRequest $request)
    {
        try {
            $data = $request->validated();
            if (!isset($data['estado'])) {
                $data['estado'] = 'disponible';
            }

            $cancha = Cancha::create($data);

            Log::info('Cancha creada', ['cancha_id' => $cancha->id, 'usuario_id' => Auth::id()]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cancha creada exitosamente',
                'data' => $cancha
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear la cancha: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la cancha'
            ], 500);
        }
    }

    /**
     * Actualizar una cancha existente (solo admin)
     * @param CanchaRequest $request
     * @param Cancha $cancha
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CanchaRequest $request, Cancha $cancha)
    {
        try {
            $data = $request->validated();

            // Verificar si hay reservas activas antes de cambiar el estado
            if (isset($data['estado']) && $data['estado'] !== 'disponible' &&
                $cancha->reservas()->where('estado', 'activa')->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se puede cambiar el estado de la cancha porque tiene reservas activas'
                ], 422);
            }

            $cancha->update($data);

            Log::info('Cancha actualizada', ['cancha_id' => $cancha->id, 'usuario_id' => Auth::id()]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cancha actualizada exitosamente',
                'data' => $cancha
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la cancha: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar la cancha'
            ], 500);
        }
    }

    /**
     * Eliminar una cancha (solo admin)
     * @param Cancha $cancha
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Cancha $cancha)
    {
        try {
            if ($cancha->reservas()->where('estado', 'activa')->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se puede eliminar la cancha porque tiene reservas activas'
                ], 422);
            }

            $cancha->delete();

            Log::info('Cancha eliminada', ['cancha_id' => $cancha->id, 'usuario_id' => Auth::id()]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cancha eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la cancha: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar la cancha'
            ], 500);
        }
    }

    /**
     * Verificar disponibilidad de una cancha
     * @param Request $request
     * @param Cancha $cancha
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificarDisponibilidad(Request $request, Cancha $cancha)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fecha' => 'required|date_format:Y-m-d|after_or_equal:today',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $disponible = $cancha->estado === 'disponible' && $cancha->estaDisponible(
                $request->fecha,
                $request->hora_inicio,
                $request->hora_fin
            );

            return response()->json([
                'status' => 'success',
                'data' => [
                    'disponible' => $disponible,
                    'cancha' => $cancha->only(['id', 'nombre', 'ubicacion', 'capacidad', 'precio_hora']),
                    'horario_solicitado' => [
                        'fecha' => $request->fecha,
                        'hora_inicio' => $request->hora_inicio,
                        'hora_fin' => $request->hora_fin
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al verificar disponibilidad: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al verificar disponibilidad'
            ], 500);
        }
    }
}
