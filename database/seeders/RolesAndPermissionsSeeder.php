<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos para Canchas
        $canchasPermissions = [
            'ver-canchas',
            'ver-cancha',
            'crear-cancha',
            'editar-cancha',
            'eliminar-cancha',
            'ver-disponibilidad'
        ];

        // Permisos para Reservas
        $reservasPermissions = [
            'ver-mis-reservas',
            'ver-todas-reservas',
            'crear-reserva',
            'ver-reserva',
            'editar-reserva',
            'cancelar-reserva'
        ];

        // Crear permisos
        foreach (array_merge($canchasPermissions, $reservasPermissions) as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Crear rol de administrador
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        // Asignar todos los permisos al admin
        $adminRole->givePermissionTo(Permission::all());

        // Crear rol de usuario
        $userRole = Role::create(['name' => 'usuario', 'guard_name' => 'api']);
        // Asignar permisos específicos al usuario
        $userRole->givePermissionTo([
            'ver-canchas',
            'ver-cancha',
            'ver-disponibilidad',
            'ver-mis-reservas',
            'crear-reserva',
            'ver-reserva',
            'editar-reserva',
            'cancelar-reserva'
        ]);
    }
}
