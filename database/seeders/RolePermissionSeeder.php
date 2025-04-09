<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',
            'ver_canchas',
            'crear_canchas',
            'editar_canchas',
            'eliminar_canchas',
            'ver_reservas',
            'crear_reservas',
            'editar_reservas',
            'eliminar_reservas',
            'ver_dashboard',
            'gestionar_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $admin = Role::create(['name' => 'admin']);
        $usuario = Role::create(['name' => 'usuario']);

        // Asignar todos los permisos al rol admin
        $admin->givePermissionTo($permissions);

        // Asignar permisos básicos al rol usuario
        $usuario->givePermissionTo([
            'ver_canchas',
            'ver_reservas',
            'crear_reservas',
            'editar_reservas',
            'eliminar_reservas',
        ]);
    }
}
