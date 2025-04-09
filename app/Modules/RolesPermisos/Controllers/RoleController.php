<?php

namespace App\Modules\RolesPermisos\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\RolesPermisos\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        try {
            Log::info('Intentando obtener roles');
            $roles = Role::with('permissions')->get();
            Log::info('Roles obtenidos exitosamente', ['count' => $roles->count()]);
            return response()->json($roles);
        } catch (\Exception $e) {
            Log::error('Error al obtener roles: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error al obtener roles: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:roles,name',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'api'
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            Log::info('Rol creado exitosamente', ['role' => $role->name]);

            return response()->json([
                'message' => 'Rol creado exitosamente',
                'role' => $role->load('permissions')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear rol: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error al crear el rol: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            return response()->json($role);
        } catch (\Exception $e) {
            Log::error('Error al obtener rol: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener el rol'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|unique:roles,name,' . $role->id,
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            if ($role->name === 'administrador') {
                return response()->json(['message' => 'No se puede modificar el rol de administrador'], 403);
            }

            $role->update(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            Log::info('Rol actualizado exitosamente', ['role' => $role->name]);

            return response()->json([
                'message' => 'Rol actualizado exitosamente',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar rol: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar el rol'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            
            if ($role->name === 'administrador' || $role->name === 'usuario') {
                return response()->json(['message' => 'No se pueden eliminar roles del sistema'], 403);
            }

            $role->delete();
            Log::info('Rol eliminado exitosamente', ['role' => $role->name]);
            return response()->json(['message' => 'Rol eliminado exitosamente']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar rol: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el rol'], 500);
        }
    }

    public function assignRole(Request $request, User $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,name'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user->syncRoles($request->roles);
            Log::info('Roles asignados exitosamente', [
                'user' => $user->email,
                'roles' => $request->roles
            ]);

            return response()->json([
                'message' => 'Roles asignados exitosamente',
                'user' => $user->load('roles.permissions')
            ]);
        } catch (\Exception $e) {
            Log::error('Error al asignar roles: ' . $e->getMessage());
            return response()->json(['message' => 'Error al asignar roles'], 500);
        }
    }

    public function getUsersWithRoles()
    {
        try {
            $users = User::with('roles.permissions')->get();
            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Error al obtener usuarios con roles: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener usuarios'], 500);
        }
    }

    public function getUserPermissions(User $user)
    {
        try {
            return response()->json([
                'user' => $user->name,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener permisos del usuario: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener permisos del usuario'], 500);
        }
    }
}
