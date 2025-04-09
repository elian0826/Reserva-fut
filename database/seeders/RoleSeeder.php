<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $userRole = Role::create(['name' => 'usuario', 'guard_name' => 'api']);

        // Crear permisos por módulo
        $permissions = [
            // Gestión de Usuarios
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'eliminar-usuarios',

            // Gestión de Roles y Permisos
            'ver-roles',
            'crear-roles',
            'editar-roles',
            'eliminar-roles',
            'ver-permisos',
            'crear-permisos',
            'editar-permisos',
            'eliminar-permisos',
            'asignar-permisos',
            'revocar-permisos',
            'asignar-roles',
            'revocar-roles',

            // Gestión de Canchas
            'ver-canchas',
            'crear-canchas',
            'editar-canchas',
            'eliminar-canchas',

            // Gestión de Reservas
            'ver-reservas',
            'crear-reservas',
            'editar-reservas',
            'eliminar-reservas',
            'ver-todas-reservas',
            'gestionar-todas-reservas',
            'ver-mis-reservas',
            'gestionar-mis-reservas'
        ];

foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Asignar todos los permisos al rol admin (Gestión total)
        $adminRole->givePermissionTo(Permission::all());
        
        // Asignar permisos básicos al rol usuario (solo gestionar sus reservas)
        $userRole->givePermissionTo([
            'ver-canchas',
            'ver-mis-reservas',
            'gestionar-mis-reservas',
            'crear-reservas',
            'editar-reservas',
            'eliminar-reservas'
        ]);
    }
}
