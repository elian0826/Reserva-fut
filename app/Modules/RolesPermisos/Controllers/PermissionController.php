<?php

namespace App\Modules\RolesPermisos\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    public function index()
    {
        try {
            $permissions = Permission::all();
            return response()->json($permissions);
        } catch (\Exception $e) {
            Log::error('Error al obtener permisos: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener permisos'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:permissions,name',
                'description' => 'nullable|string'
            ]);

            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'api',
                'description' => $request->description ?? null
            ]);

            return response()->json([
                'message' => 'Permiso creado exitosamente',
                'permission' => $permission
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear permiso: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear el permiso'], 500);
        }
    }

    public function show(Permission $permission)
    {
        try {
            return response()->json($permission);
        } catch (\Exception $e) {
            Log::error('Error al obtener permiso: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener el permiso'], 500);
        }
    }

    public function update(Request $request, Permission $permission)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:permissions,name,' . $permission->id,
                'description' => 'nullable|string'
            ]);

            // Verificar si es un permiso del sistema
            $systemPermissions = [
                'ver-usuarios', 'crear-usuarios', 'editar-usuarios', 'eliminar-usuarios',
                'ver-roles', 'crear-roles', 'editar-roles', 'eliminar-roles',
                'ver-permisos', 'crear-permisos', 'editar-permisos', 'eliminar-permisos',
                'ver-canchas', 'crear-canchas', 'editar-canchas', 'eliminar-canchas',
                'ver-reservas', 'crear-reservas', 'editar-reservas', 'eliminar-reservas',
                'ver-todas-reservas', 'ver-dashboard', 'ver-estadisticas', 'gestionar-sistema'
            ];

            if (in_array($permission->name, $systemPermissions)) {
                return response()->json(['message' => 'No se pueden modificar permisos del sistema'], 403);
            }

            $permission->update([
                'name' => $request->name,
                'description' => $request->description ?? $permission->description
            ]);

            return response()->json([
                'message' => 'Permiso actualizado exitosamente',
                'permission' => $permission
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar permiso: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar el permiso'], 500);
        }
    }

    public function destroy(Permission $permission)
    {
        try {
            // Verificar si es un permiso del sistema
            $systemPermissions = [
                'ver-usuarios', 'crear-usuarios', 'editar-usuarios', 'eliminar-usuarios',
                'ver-roles', 'crear-roles', 'editar-roles', 'eliminar-roles',
                'ver-permisos', 'crear-permisos', 'editar-permisos', 'eliminar-permisos',
                'ver-canchas', 'crear-canchas', 'editar-canchas', 'eliminar-canchas',
                'ver-reservas', 'crear-reservas', 'editar-reservas', 'eliminar-reservas',
                'ver-todas-reservas', 'ver-dashboard', 'ver-estadisticas', 'gestionar-sistema'
            ];

            if (in_array($permission->name, $systemPermissions)) {
                return response()->json(['message' => 'No se pueden eliminar permisos del sistema'], 403);
            }

            $permission->delete();
            return response()->json(['message' => 'Permiso eliminado exitosamente']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar permiso: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el permiso'], 500);
        }
    }

    public function getSystemPermissions()
    {
        try {
            $systemPermissions = [
                'Usuarios' => [
                    'ver-usuarios', 'crear-usuarios', 'editar-usuarios', 'eliminar-usuarios'
                ],
                'Roles' => [
                    'ver-roles', 'crear-roles', 'editar-roles', 'eliminar-roles'
                ],
                'Permisos' => [
                    'ver-permisos', 'crear-permisos', 'editar-permisos', 'eliminar-permisos'
                ],
                'Canchas' => [
                    'ver-canchas', 'crear-canchas', 'editar-canchas', 'eliminar-canchas'
                ],
                'Reservas' => [
                    'ver-reservas', 'crear-reservas', 'editar-reservas', 'eliminar-reservas',
                    'ver-todas-reservas'
                ],
                'Sistema' => [
                    'ver-dashboard', 'ver-estadisticas', 'gestionar-sistema'
                ]
            ];

            return response()->json($systemPermissions);
        } catch (\Exception $e) {
            Log::error('Error al obtener permisos del sistema: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener permisos del sistema'], 500);
        }
    }
}
