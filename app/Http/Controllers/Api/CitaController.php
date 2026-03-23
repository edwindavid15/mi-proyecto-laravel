<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CitaRequest;
use App\Http\Resources\CitaResource;
use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isCliente()) {
            $citas = Cita::where('cliente_id', $user->id)
                        ->with(['peluqueria', 'servicio', 'peluquero', 'cliente'])
                        ->orderBy('fecha', 'desc')
                        ->orderBy('hora', 'desc')
                        ->get();
        } else {
            $citas = Cita::where('peluquero_id', $user->id)
                        ->with(['peluqueria', 'servicio', 'peluquero', 'cliente'])
                        ->orderBy('fecha', 'desc')
                        ->orderBy('hora', 'desc')
                        ->get();
        }

        return CitaResource::collection($citas);
    }

    public function store(CitaRequest $request)
    {
        $user = Auth::user();

        $peluquero = User::where('id', $request->peluquero_id)
            ->where('role', 'peluquero')
            ->first();

        if (!$peluquero) {
            return response()->json(['message' => 'Peluquero no válido'], 400);
        }

        $servicio = Servicio::findOrFail($request->servicio_id);

        $conflicto = Cita::where('peluquero_id', $request->peluquero_id)
                         ->where('fecha', $request->fecha)
                         ->where('hora', $request->hora)
                         ->exists();

        if ($conflicto) {
            return response()->json(['message' => 'El horario seleccionado no está disponible'], 409);
        }

        $cita = Cita::create([
            'cliente_id' => $user->id,
            'peluquero_id' => $request->peluquero_id,
            'peluqueria_id' => $request->peluqueria_id,
            'servicio_id' => $request->servicio_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'estado' => 'pendiente',
        ]);

        return response()->json([
            'cita' => new CitaResource($cita->load(['peluqueria', 'servicio', 'peluquero', 'cliente'])),
            'message' => 'Cita creada correctamente'
        ], 201);
    }

    public function show(Cita $cita)
    {
        $user = Auth::user();

        if ($cita->cliente_id !== $user->id && $cita->peluquero_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes acceso a esta cita'], 403);
        }

        return new CitaResource($cita->load(['peluqueria', 'servicio', 'peluquero', 'cliente']));
    }

    public function update(CitaRequest $request, Cita $cita)
    {
        $user = Auth::user();

        if ($cita->peluquero_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos para actualizar esta cita'], 403);
        }

        $request->validate(['estado' => 'required|in:pendiente,confirmada,completada,cancelada']);

        $cita->update(['estado' => $request->estado]);

        return response()->json([
            'cita' => new CitaResource($cita->load(['peluqueria', 'servicio', 'peluquero', 'cliente'])),
            'message' => 'Cita actualizada correctamente'
        ]);
    }

    public function destroy(Cita $cita)
    {
        $user = Auth::user();

        if ($cita->cliente_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos para cancelar esta cita'], 403);
        }

        if ($cita->estado !== 'pendiente') {
            return response()->json(['message' => 'No se puede cancelar una cita que ya fue confirmada'], 400);
        }

        $cita->update(['estado' => 'cancelada']);

        return response()->json(['message' => 'Cita cancelada correctamente']);
    }
}
