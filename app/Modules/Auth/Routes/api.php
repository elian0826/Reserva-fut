<?php

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Auth\Controllers\PasswordResetController;
use App\Modules\Auth\Controllers\VerificationController;
use App\Modules\Auth\Controllers\RoleController;
use App\Modules\Auth\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    // Rutas de autenticación
    Route::prefix('auth')->group(function () {
        // Rutas públicas
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);

        // Rutas protegidas
        Route::middleware('auth:api')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'user']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('roles', [AuthController::class, 'getRoles']);

            // Rutas de administrador
            Route::middleware('role:admin')->group(function () {
                Route::get('roles', [RolePermissionController::class, 'listRoles']);
                Route::post('roles', [RolePermissionController::class, 'createRole']);
                Route::put('roles/{role}', [RolePermissionController::class, 'updateRole']);
                Route::delete('roles/{role}', [RolePermissionController::class, 'deleteRole']);
                Route::get('permissions', [RolePermissionController::class, 'listPermissions']);
                Route::post('roles/{role}/permissions', [RolePermissionController::class, 'assignPermissionsToRole']);
                Route::get('users', [AuthController::class, 'getAllUsers']);
                Route::put('users/{user}', [AuthController::class, 'updateUser']);
                Route::delete('users/{user}', [AuthController::class, 'deleteUser']);
                Route::post('users/{user}/roles', [RolePermissionController::class, 'assignRoleToUser']);
                Route::get('users/{user}/permissions', [RolePermissionController::class, 'getUserPermissions']);
            });

            // Verificación de email
            Route::post('email/verification-notification', [VerificationController::class, 'sendVerificationEmail']);
            Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
            Route::post('email/verify/resend', [VerificationController::class, 'resend'])->name('verification.resend');
        });
    });
});
