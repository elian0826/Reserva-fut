<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Reservas\Controllers\ReservaController;

Route::prefix('api')->middleware(['api'])->group(function () {
    // Grupo de rutas para reservas
    Route::middleware(['auth:api'])->group(function () {
        // Rutas para usuarios y administradores
        Route::prefix('reservas')->group(function () {
            Route::get('/', [ReservaController::class, 'misReservas'])
                ->name('reservas.index');

            Route::post('/', [ReservaController::class, 'store'])
                ->name('reservas.store');

            Route::get('/{reserva}', [ReservaController::class, 'show'])
                ->name('reservas.show');

            Route::put('/{reserva}', [ReservaController::class, 'update'])
                ->middleware('role:usuario')
                ->name('reservas.update');

            Route::delete('/{reserva}', [ReservaController::class, 'destroy'])
                ->middleware('role:usuario')
                ->name('reservas.destroy');
        });

        // Ruta especial para administradores
        Route::get('/admin/reservas', [ReservaController::class, 'index'])
            ->middleware('role:admin')
            ->name('admin.reservas.index');
    });
});
