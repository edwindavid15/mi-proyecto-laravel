<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirigir todas las rutas web a la documentación de la API
Route::get('/', function () {
    return response()->json([
        'message' => 'BarberApp API - StyleRadar',
        'version' => '1.0.0',
        'documentation' => url('/api/documentation'),
        'endpoints' => [
            'auth' => [
                'login' => 'POST /api/login',
                'register' => 'POST /api/register',
                'logout' => 'POST /api/logout',
                'profile' => 'GET /api/profile',
            ],
            'services' => 'GET|POST /api/servicios',
            'appointments' => 'GET|POST /api/citas',
            'barbershops' => 'GET|POST /api/peluquerias',
        ]
    ]);
});

Route::get('/api/documentation', function () {
    return response()->json([
        'title' => 'BarberApp API Documentation',
        'description' => 'API RESTful para el sistema de reservas de barbería StyleRadar',
        'base_url' => url('/api'),
        'authentication' => 'Bearer Token (Laravel Sanctum)',
        'version' => '1.0.0',
    ]);
});
