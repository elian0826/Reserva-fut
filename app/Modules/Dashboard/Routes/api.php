<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;

// Rutas protegidas que requieren autenticación y rol de administrador
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/estadisticas', [DashboardController::class, 'estadisticas']);
    Route::get('/usuarios', [DashboardController::class, 'usuariosRegistrados']);
    Route::get('/canchas/resumen', [DashboardController::class, 'resumenCanchas']);
    Route::get('/reportes/mensual', [DashboardController::class, 'reporteMensual']);
});
