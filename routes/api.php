<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServicioController;
use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\PeluqueriaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de autenticación
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Servicios
    Route::apiResource('servicios', ServicioController::class);

    // Citas
    Route::apiResource('citas', CitaController::class);

    // Peluquerías
    Route::apiResource('peluquerias', PeluqueriaController::class);
    Route::get('peluquerias/nearby', [PeluqueriaController::class, 'nearby']);
    Route::get('peluqueros/nearby', [PeluqueriaController::class, 'peluquerosNearby']);
    Route::post('peluquerias/{id}/add-peluquero', [PeluqueriaController::class, 'addPeluquero']);
    Route::delete('peluquerias/{id}/remove-peluquero', [PeluqueriaController::class, 'removePeluquero']);

    // Perfil
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
});