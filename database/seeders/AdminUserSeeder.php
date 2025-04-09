<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('admin123456'),
            'email_verified_at' => now(),
            'estado' => 'activo'
        ]);

        // Asignar rol de administrador
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        }
        $admin->assignRole($adminRole);

        // Asegurar que tenga todos los permisos
        $admin->syncPermissions(\Spatie\Permission\Models\Permission::all());
    }
}
