<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\User;
use App\Models\Servicio;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = User::clientes()->get();
        $servicios = Servicio::disponibles()->get();

        if ($clientes->isEmpty() || $servicios->isEmpty()) {
            return; // No crear citas si no hay clientes o servicios
        }

        $estados = [
            Cita::ESTADO_COMPLETADA,
            Cita::ESTADO_CONFIRMADA,
            Cita::ESTADO_PENDIENTE,
            Cita::ESTADO_CANCELADA,
        ];

        // Crear citas para los próximos 30 días
        for ($i = 0; $i < 50; $i++) {
            $cliente = $clientes->random();
            $servicio = $servicios->random();

            // Asegurarse de que el peluquero esté disponible
            if (!$servicio->peluquero) {
                continue;
            }

            $fecha = Carbon::now()->addDays(rand(0, 30));
            $hora = sprintf('%02d:%02d', rand(9, 19), rand(0, 3) * 15); // Horas entre 9:00 y 19:00 en intervalos de 15 min

            // Verificar que no haya conflicto de horario
            $conflicto = Cita::where('peluquero_id', $servicio->peluquero_id)
                             ->where('fecha', $fecha->toDateString())
                             ->where('hora', $hora)
                             ->whereIn('estado', [Cita::ESTADO_PENDIENTE, Cita::ESTADO_CONFIRMADA])
                             ->exists();

            if ($conflicto) {
                continue; // Saltar si hay conflicto
            }

            $estado = $estados[array_rand($estados)];

            // Para citas futuras, no permitir estados completados
            if ($fecha->isFuture() && $estado === Cita::ESTADO_COMPLETADA) {
                $estado = Cita::ESTADO_CONFIRMADA;
            }

            // Para citas pasadas, no permitir estados pendientes
            if ($fecha->isPast() && $estado === Cita::ESTADO_PENDIENTE) {
                $estado = $fecha->isToday() ? Cita::ESTADO_CONFIRMADA : Cita::ESTADO_COMPLETADA;
            }

            $precioFinal = null;
            if ($estado === Cita::ESTADO_COMPLETADA) {
                $precioFinal = $servicio->precio;
            }

            Cita::create([
                'cliente_id' => $cliente->id,
                'peluquero_id' => $servicio->peluquero_id,
                'peluqueria_id' => $servicio->peluqueria_id,
                'servicio_id' => $servicio->id,
                'fecha' => $fecha->toDateString(),
                'hora' => $hora,
                'estado' => $estado,
                'precio_final' => $precioFinal,
                'notas' => rand(0, 2) === 0 ? 'Cliente pidió atención especial' : null,
            ]);
        }

        // Crear algunas citas para hoy específicamente
        for ($i = 0; $i < 10; $i++) {
            $cliente = $clientes->random();
            $servicio = $servicios->random();

            if (!$servicio->peluquero) {
                continue;
            }

            $hora = sprintf('%02d:%02d', rand(10, 17), rand(0, 3) * 15);

            $conflicto = Cita::where('peluquero_id', $servicio->peluquero_id)
                             ->where('fecha', now()->toDateString())
                             ->where('hora', $hora)
                             ->whereIn('estado', [Cita::ESTADO_PENDIENTE, Cita::ESTADO_CONFIRMADA])
                             ->exists();

            if (!$conflicto) {
                Cita::create([
                    'cliente_id' => $cliente->id,
                    'peluquero_id' => $servicio->peluquero_id,
                    'peluqueria_id' => $servicio->peluqueria_id,
                    'servicio_id' => $servicio->id,
                    'fecha' => now()->toDateString(),
                    'hora' => $hora,
                    'estado' => rand(0, 1) ? Cita::ESTADO_CONFIRMADA : Cita::ESTADO_PENDIENTE,
                    'notas' => 'Cita para hoy',
                ]);
            }
        }
    }
}