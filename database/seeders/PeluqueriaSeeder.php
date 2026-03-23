<?php

namespace Database\Seeders;

use App\Models\Peluqueria;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeluqueriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $duenos = User::duenos()->get();

        $peluquerias = [
            [
                'nombre' => 'Barbería Central',
                'descripcion' => 'La mejor barbería del centro con servicios premium y ambiente moderno.',
                'direccion' => 'Calle Principal 123, Centro',
                'telefono' => '+1234567890',
                'email' => 'central@barberapp.com',
                'latitud' => 40.7128,
                'longitud' => -74.0060,
                'horario_apertura' => '09:00',
                'horario_cierre' => '20:00',
                'is_active' => true,
            ],
            [
                'nombre' => 'StyleMasters',
                'descripcion' => 'Especialistas en cortes modernos y tratamientos capilares.',
                'direccion' => 'Avenida Moderna 456, Zona Norte',
                'telefono' => '+1234567891',
                'email' => 'stylemasters@barberapp.com',
                'latitud' => 40.7589,
                'longitud' => -73.9851,
                'horario_apertura' => '08:00',
                'horario_cierre' => '19:00',
                'is_active' => true,
            ],
            [
                'nombre' => 'Elite Cuts',
                'descripcion' => 'Cortes de élite para caballeros exigentes.',
                'direccion' => 'Plaza Elegante 789, Zona Sur',
                'telefono' => '+1234567892',
                'email' => 'elitecuts@barberapp.com',
                'latitud' => 40.7505,
                'longitud' => -73.9934,
                'horario_apertura' => '10:00',
                'horario_cierre' => '21:00',
                'is_active' => true,
            ],
        ];

        foreach ($peluquerias as $index => $peluqueriaData) {
            $dueno = $duenos->get($index % $duenos->count());

            $peluqueria = Peluqueria::create(array_merge($peluqueriaData, [
                'owner_id' => $dueno->id,
            ]));

            // Asignar peluqueros aleatorios a cada peluquería
            $peluqueros = User::peluqueros()->inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($peluqueros as $peluquero) {
                $peluqueria->peluqueros()->attach($peluquero->id);
            }
        }
    }
}