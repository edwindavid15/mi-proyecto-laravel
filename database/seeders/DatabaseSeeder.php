<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Iniciando seeding de BarberApp...');

        $this->command->info('Creando usuarios...');
        $this->call(UserSeeder::class);

        $this->command->info('Creando peluquerías...');
        $this->call(PeluqueriaSeeder::class);

        $this->command->info('Creando servicios...');
        $this->call(ServicioSeeder::class);

        $this->command->info('Creando citas...');
        $this->call(CitaSeeder::class);

        $this->command->info('Seeding completado exitosamente!');
        $this->command->info('');
        $this->command->info('Datos de prueba creados:');
        $this->command->info('- 1 Administrador');
        $this->command->info('- 2 Dueños de peluquería');
        $this->command->info('- 3 Peluqueros');
        $this->command->info('- 3 Clientes');
        $this->command->info('- 3 Peluquerías');
        $this->command->info('- Múltiples servicios');
        $this->command->info('- 50+ citas de prueba');
        $this->command->info('');
        $this->command->info('Credenciales de admin: admin@barberapp.com / password');
    }
}
