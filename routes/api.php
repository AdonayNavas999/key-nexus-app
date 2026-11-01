<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CredencialController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas Protegidas por Sanctum (Requieren Token de inicio de sesión)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gestión de Credenciales y su Bitácora de Historial
    Route::get('credenciales/exportar/pdf', [CredencialController::class, 'exportarPdf']);
    Route::apiResource('credenciales', CredencialController::class);
    Route::get('credenciales/{id}/historial', [CredencialController::class, 'historial']);
});
