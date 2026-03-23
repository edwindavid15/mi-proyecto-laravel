<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isPeluquero()) {
            $servicios = Servicio::where('peluquero_id', $user->id)->with(['peluqueria', 'peluquero'])->get();
        } elseif ($user->isDueno()) {
            $servicios = Servicio::whereIn('peluqueria_id', $user->peluquerias->pluck('id'))->with(['peluqueria', 'peluquero'])->get();
        } else {
            // Clientes ven todos los servicios disponibles
            $servicios = Servicio::with(['peluqueria', 'peluquero'])->get();
        }

        return response()->json([
            'servicios' => $servicios
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isPeluquero() && !$user->isDueno()) {
            return response()->json(['message' => 'No tienes permisos para crear servicios'], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'duracion' => 'nullable|integer|min:1',
            'descripcion' => 'nullable|string',
            'peluqueria_id' => 'required_if:is_dueno,true|exists:peluquerias,id',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'duracion' => $request->duracion,
            'peluquero_id' => $user->id,
        ];

        // Si es dueño, puede asignar a una peluquería específica
        if ($user->isDueno() && $request->has('peluqueria_id')) {
            // Verificar que la peluquería pertenece al dueño
            if (!$user->peluquerias()->where('id', $request->peluqueria_id)->exists()) {
                return response()->json(['message' => 'No tienes permisos sobre esta peluquería'], 403);
            }
            $data['peluqueria_id'] = $request->peluqueria_id;
        }

        $servicio = Servicio::create($data);

        return response()->json([
            'servicio' => $servicio->load(['peluqueria', 'peluquero']),
            'message' => 'Servicio creado correctamente'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $servicio = Servicio::with(['peluqueria', 'peluquero'])->findOrFail($id);

        return response()->json([
            'servicio' => $servicio
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $servicio = Servicio::findOrFail($id);
        $user = Auth::user();

        // Verificar permisos: solo el peluquero que creó el servicio o el dueño pueden editarlo
        if ($servicio->peluquero_id !== $user->id && !$user->isDueno()) {
            return response()->json(['message' => 'No tienes permisos para editar este servicio'], 403);
        }

        // Si es dueño, verificar que el servicio pertenece a una de sus peluquerías
        if ($user->isDueno() && !$user->peluquerias()->where('id', $servicio->peluqueria_id)->exists()) {
            return response()->json(['message' => 'No tienes permisos sobre este servicio'], 403);
        }

        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'precio' => 'sometimes|required|numeric|min:0',
            'duracion' => 'nullable|integer|min:1',
            'descripcion' => 'nullable|string',
        ]);

        $servicio->update($request->only(['nombre', 'descripcion', 'precio', 'duracion']));

        return response()->json([
            'servicio' => $servicio->load(['peluqueria', 'peluquero']),
            'message' => 'Servicio actualizado correctamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $user = Auth::user();

        // Verificar permisos: solo el peluquero que creó el servicio o el dueño pueden eliminarlo
        if ($servicio->peluquero_id !== $user->id && !$user->isDueno()) {
            return response()->json(['message' => 'No tienes permisos para eliminar este servicio'], 403);
        }

        // Si es dueño, verificar que el servicio pertenece a una de sus peluquerías
        if ($user->isDueno() && !$user->peluquerias()->where('id', $servicio->peluqueria_id)->exists()) {
            return response()->json(['message' => 'No tienes permisos sobre este servicio'], 403);
        }

        $servicio->delete();

        return response()->json([
            'message' => 'Servicio eliminado correctamente'
        ]);
    }
}