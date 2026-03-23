<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@barberapp.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'phone' => '+1234567890',
            'is_active' => true,
        ]);

        // Crear dueños de peluquerías
        $duenos = [
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.dueno@barberapp.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DUENO,
                'phone' => '+1234567891',
                'is_active' => true,
            ],
            [
                'name' => 'María González',
                'email' => 'maria.dueno@barberapp.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DUENO,
                'phone' => '+1234567892',
                'is_active' => true,
            ],
        ];

        foreach ($duenos as $dueno) {
            User::create($dueno);
        }

        // Crear peluqueros
        $peluqueros = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.peluquero@barberapp.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PELUQUERO,
                'phone' => '+1234567893',
                'is_active' => true,
                'is_online' => true,
                'latitud' => 40.7130,
                'longitud' => -74.0062,
            ],
            [
                'name' => 'Ana López',
                'email' => 'ana.peluquero@barberapp.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PELUQUERO,
                'phone' => '+1234567894',
                'is_active' => true,
                'is_online' => false,
                'latitud' => 40.7580,
                'longitud' => -73.9850,
            ],
            [
                'name' => 'Pedro Martínez',
                'email' => 'pedro.peluquero@barberapp.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PELUQUERO,
                'phone' => '+1234567895',
                'is_active' => true,
                'is_online' => true,
                'latitud' => 40.7510,
                'longitud' => -73.9930,
            ],
        ];

        foreach ($peluqueros as $peluquero) {
            User::create($peluquero);
        }

        // Crear clientes
        $clientes = [
            [
                'name' => 'Luis García',
                'email' => 'luis.cliente@barberapp.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CLIENTE,
                'phone' => '+1234567896',
                'is_active' => true,
            ],
            [
                'name' => 'Carmen Sánchez',
                'email' => 'carmen.cliente@barberapp.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CLIENTE,
                'phone' => '+1234567897',
                'is_active' => true,
            ],
            [
                'name' => 'Roberto Díaz',
                'email' => 'roberto.cliente@barberapp.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CLIENTE,
                'phone' => '+1234567898',
                'is_active' => true,
            ],
        ];

        foreach ($clientes as $cliente) {
            User::create($cliente);
        }
    }
}