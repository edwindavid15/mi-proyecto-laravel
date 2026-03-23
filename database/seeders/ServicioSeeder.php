<?php

namespace Database\Seeders;

use App\Models\Servicio;
use App\Models\User;
use App\Models\Peluqueria;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviciosData = [
            [
                'nombre' => 'Corte de Cabello Clásico',
                'descripcion' => 'Corte tradicional con tijeras y máquina.',
                'precio' => 25.00,
                'duracion' => 30,
            ],
            [
                'nombre' => 'Corte Moderno',
                'descripcion' => 'Corte contemporáneo con técnicas modernas.',
                'precio' => 35.00,
                'duracion' => 45,
            ],
            [
                'nombre' => 'Afeitado Completo',
                'descripcion' => 'Afeitado tradicional con navaja y tratamientos.',
                'precio' => 20.00,
                'duracion' => 25,
            ],
            [
                'nombre' => 'Arreglo de Barba',
                'descripcion' => 'Recorte y perfilado profesional de barba.',
                'precio' => 15.00,
                'duracion' => 20,
            ],
            [
                'nombre' => 'Corte + Barba',
                'descripcion' => 'Paquete completo: corte de cabello y arreglo de barba.',
                'precio' => 40.00,
                'duracion' => 50,
            ],
            [
                'nombre' => 'Tratamiento Capilar',
                'descripcion' => 'Tratamiento revitalizante para el cabello.',
                'precio' => 30.00,
                'duracion' => 40,
            ],
            [
                'nombre' => 'Tinte de Cabello',
                'descripcion' => 'Coloración profesional del cabello.',
                'precio' => 50.00,
                'duracion' => 90,
            ],
            [
                'nombre' => 'Lavado y Peinado',
                'descripcion' => 'Lavado profesional y peinado moderno.',
                'precio' => 18.00,
                'duracion' => 25,
            ],
        ];

        $peluquerias = Peluqueria::all();

        foreach ($peluquerias as $peluqueria) {
            // Obtener peluqueros de esta peluquería
            $peluqueros = $peluqueria->peluqueros;

            if ($peluqueros->isEmpty()) {
                continue; // Saltar si no hay peluqueros
            }

            // Crear servicios para cada peluquería
            foreach ($serviciosData as $servicioData) {
                // Asignar un peluquero aleatorio de la peluquería
                $peluquero = $peluqueros->random();

                Servicio::create(array_merge($servicioData, [
                    'peluquero_id' => $peluquero->id,
                    'peluqueria_id' => $peluqueria->id,
                    'is_active' => true,
                ]));
            }
        }

        // Crear algunos servicios adicionales para peluqueros sin peluquería asignada
        $peluquerosSinPeluqueria = User::peluqueros()
            ->whereDoesntHave('peluquerias')
            ->get();

        foreach ($peluquerosSinPeluqueria as $peluquero) {
            $serviciosAleatorios = collect($serviciosData)->random(rand(2, 4));

            foreach ($serviciosAleatorios as $servicioData) {
                Servicio::create(array_merge($servicioData, [
                    'peluquero_id' => $peluquero->id,
                    'peluqueria_id' => null,
                    'is_active' => true,
                ]));
            }
        }
    }
}