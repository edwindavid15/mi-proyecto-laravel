<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Peluqueria;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $userId = $user->getKey();

        if ($user->isCliente()) {
            $citas = Cita::where('cliente_id', $userId)
                        ->with(['peluqueria', 'servicio', 'peluquero', 'cliente'])
                        ->orderBy('fecha', 'desc')
                        ->orderBy('hora', 'desc')
                        ->get();
        } else {
            // Peluqueros y dueños ven las citas donde son el peluquero asignado
            $citas = Cita::where('peluquero_id', $userId)
                        ->with(['peluqueria', 'servicio', 'peluquero', 'cliente'])
                        ->orderBy('fecha', 'desc')
                        ->orderBy('hora', 'desc')
                        ->get();
        }

        return response()->json([
            'citas' => $citas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'peluqueria_id' => 'required|exists:peluquerias,id',
            'servicio_id' => 'required|exists:servicios,id',
            'peluquero_id' => 'required|exists:users,id',
            'fecha' => 'required|date|after:today',
            'hora' => 'required|date_format:H:i',
        ]);

        // Verificar que el peluquero seleccionado existe y tiene ese rol
        $peluquero = User::where('id', $request->peluquero_id)->where('role', 'peluquero')->first();
        if (!$peluquero) {
            return response()->json(['message' => 'Peluquero no válido'], 400);
        }

        // Verificar que el servicio existe
        $servicio = Servicio::findOrFail($request->servicio_id);

        // Verificar que no haya conflicto de horario (misma fecha, hora y peluquero)
        $conflicto = Cita::where('peluquero_id', $request->peluquero_id)
                         ->where('fecha', $request->fecha)
                         ->where('hora', $request->hora)
                         ->exists();

        if ($conflicto) {
            return response()->json(['message' => 'El horario seleccionado no está disponible'], 409);
        }

        $cita = Cita::create([
            'cliente_id' => $user->getKey(),
            'peluquero_id' => $request->peluquero_id,
            'peluqueria_id' => $request->peluqueria_id,
            'servicio_id' => $request->servicio_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'estado' => 'pendiente',
        ]);

        return response()->json([
            'cita' => $cita->load(['peluqueria', 'servicio', 'peluquero', 'cliente']),
            'message' => 'Cita creada correctamente'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $cita = Cita::with(['peluqueria', 'servicio', 'peluquero', 'cliente'])->findOrFail($id);

        // Verificar que el usuario tenga acceso a esta cita
        $userId = $user->getKey();
        if ($cita->cliente_id !== $userId && $cita->peluquero_id !== $userId) {
            return response()->json(['message' => 'No tienes acceso a esta cita'], 403);
        }

        return response()->json([
            'cita' => $cita
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $cita = Cita::findOrFail($id);

        // Solo el peluquero asignado puede actualizar el estado de la cita
        $userId = $user->getKey();
        if ($cita->peluquero_id !== $userId) {
            return response()->json(['message' => 'No tienes permisos para actualizar esta cita'], 403);
        }

        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,completada,cancelada',
        ]);

        $cita->update([
            'estado' => $request->estado,
        ]);

        return response()->json([
            'cita' => $cita->load(['peluqueria', 'servicio', 'peluquero', 'cliente']),
            'message' => 'Cita actualizada correctamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $cita = Cita::findOrFail($id);

        // Solo el cliente que creó la cita puede cancelarla
        $userId = $user->getKey();
        if ($cita->cliente_id !== $userId) {
            return response()->json(['message' => 'No tienes permisos para cancelar esta cita'], 403);
        }

        // Solo se puede cancelar si está pendiente
        if ($cita->estado !== 'pendiente') {
            return response()->json(['message' => 'No se puede cancelar una cita que ya fue confirmada'], 400);
        }

        $cita->update(['estado' => 'cancelada']);

        return response()->json([
            'message' => 'Cita cancelada correctamente'
        ]);
    }
}