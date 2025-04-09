<?php

use App\Modules\RolesPermisos\Controllers\RoleController;
use App\Modules\RolesPermisos\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->prefix('api/roles-permisos')->group(function () {
    // Rutas para roles
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
    
    // Rutas para asignación de roles
    Route::post('/roles/users/{user}/assign', [RoleController::class, 'assignRole']);
    Route::get('/users/roles', [RoleController::class, 'getUsersWithRoles']);
    Route::get('/users/{user}/permissions', [RoleController::class, 'getUserPermissions']);

    // Rutas para permisos
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/permissions', [PermissionController::class, 'store']);
    Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
    Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);
    Route::get('/permissions/system', [PermissionController::class, 'getSystemPermissions']);
});
