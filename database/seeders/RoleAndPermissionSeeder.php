<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos para canchas
        Permission::create(['name' => 'ver-canchas', 'guard_name' => 'api']);
        Permission::create(['name' => 'crear-canchas', 'guard_name' => 'api']);
        Permission::create(['name' => 'editar-canchas', 'guard_name' => 'api']);
        Permission::create(['name' => 'eliminar-canchas', 'guard_name' => 'api']);
        Permission::create(['name' => 'ver-disponibilidad', 'guard_name' => 'api']);

        // Crear rol de administrador
        $roleAdmin = Role::create(['name' => 'admin', 'guard_name' => 'api']);

        // Asignar todos los permisos al rol de administrador
        $roleAdmin->givePermissionTo(Permission::all());

        // Crear rol de usuario
        $roleUser = Role::create(['name' => 'usuario', 'guard_name' => 'api']);

        // Asignar permisos básicos al rol de usuario
        $roleUser->givePermissionTo([
            'ver-canchas',
            'ver-disponibilidad'
        ]);
    }
}
