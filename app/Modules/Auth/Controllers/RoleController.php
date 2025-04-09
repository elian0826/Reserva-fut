<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::with('permissions')->get();
            return response()->json($roles);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener roles', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
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

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
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

    public function destroy(Role $role)
    {
        try {
            if ($role->name === 'admin') {
                return response()->json(['message' => 'No se puede eliminar el rol de administrador'], 403);
            }

            $role->delete();
            return response()->json(['message' => 'Rol eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar rol', 'error' => $e->getMessage()], 500);
        }
    }

    public function permissions()
    {
        try {
            $permissions = Permission::all();
            return response()->json($permissions);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener permisos', 'error' => $e->getMessage()], 500);
        }
    }
}
