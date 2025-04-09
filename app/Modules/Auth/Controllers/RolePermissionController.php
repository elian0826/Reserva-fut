<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    // Gestión de Roles
    public function listRoles()
    {
        try {
            $roles = Role::with('permissions')->get();
            return response()->json($roles);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener roles', 'error' => $e->getMessage()], 500);
        }
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);
            $role->givePermissionTo($request->permissions);

            return response()->json([
                'message' => 'Rol creado exitosamente',
                'role' => $role->load('permissions')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear rol', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            if ($role->name === 'admin') {
                return response()->json(['message' => 'No se puede modificar el rol de administrador'], 403);
            }

            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions);

            return response()->json([
                'message' => 'Rol actualizado exitosamente',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar rol', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteRole(Role $role)
    {
        try {
            if ($role->name === 'admin' || $role->name === 'usuario') {
                return response()->json(['message' => 'No se pueden eliminar roles del sistema'], 403);
            }

            $role->delete();
            return response()->json(['message' => 'Rol eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar rol', 'error' => $e->getMessage()], 500);
        }
    }

    // Gestión de Permisos
    public function listPermissions()
    {
        try {
            $permissions = Permission::all();
            return response()->json($permissions);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener permisos', 'error' => $e->getMessage()], 500);
        }
    }

    // Gestión de Asignación de Roles y Permisos
    public function assignRoleToUser(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        try {
            $user->syncRoles($request->roles);
            return response()->json([
                'message' => 'Roles asignados exitosamente',
                'user' => $user->load('roles', 'permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al asignar roles', 'error' => $e->getMessage()], 500);
        }
    }

    public function assignPermissionsToRole(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            if ($role->name === 'admin') {
                return response()->json(['message' => 'No se pueden modificar los permisos del rol administrador'], 403);
            }

            $role->syncPermissions($request->permissions);
            return response()->json([
                'message' => 'Permisos asignados exitosamente',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al asignar permisos', 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserPermissions(User $user)
    {
        try {
            return response()->json([
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener permisos', 'error' => $e->getMessage()], 500);
        }
    }
}
