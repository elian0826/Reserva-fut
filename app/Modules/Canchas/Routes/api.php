<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Canchas\Controllers\CanchaController;

Route::group([
    'prefix' => 'api/canchas',
    'middleware' => ['api', 'auth:api']
], function () {
    // Rutas públicas (requieren autenticación)
    Route::get('/', [CanchaController::class, 'index'])
        ->name('canchas.index')
        ->middleware('permission:ver canchas');

    Route::get('/disponibles', [CanchaController::class, 'disponibles'])
        ->name('canchas.disponibles')
        ->middleware('permission:ver canchas');

    Route::get('/{cancha}', [CanchaController::class, 'show'])
        ->name('canchas.show')
        ->where('cancha', '[0-9]+')
        ->middleware('permission:ver canchas');

    Route::get('/{cancha}/disponibilidad', [CanchaController::class, 'verificarDisponibilidad'])
        ->name('canchas.disponibilidad')
        ->where('cancha', '[0-9]+')
        ->middleware('permission:ver canchas');

    // Rutas protegidas (solo admin)
    Route::group(['middleware' => ['role:admin']], function () {
        Route::post('/', [CanchaController::class, 'store'])
            ->name('canchas.store')
            ->middleware('permission:crear canchas');

        Route::put('/{cancha}', [CanchaController::class, 'update'])
            ->name('canchas.update')
            ->where('cancha', '[0-9]+')
            ->middleware('permission:editar canchas');

        Route::delete('/{cancha}', [CanchaController::class, 'destroy'])
            ->name('canchas.destroy')
            ->where('cancha', '[0-9]+')
            ->middleware('permission:eliminar canchas');
    });
});
