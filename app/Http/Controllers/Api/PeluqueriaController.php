<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peluqueria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeluqueriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isDueno()) {
            // Dueños ven solo sus peluquerías
            $peluquerias = $user->ownedPeluquerias()->with(['owner', 'peluqueros'])->get();
        } elseif ($user->isPeluquero()) {
            // Peluqueros ven las peluquerías donde trabajan
            $peluquerias = $user->peluquerias()->with(['owner', 'peluqueros'])->get();
        } else {
            // Clientes ven todas las peluquerías activas y disponibles
            $peluquerias = Peluqueria::where('is_active', true)
                ->with(['owner', 'peluqueros'])
                ->get();
        }

        return response()->json([
            'peluquerias' => $peluquerias
        ]);
    }

    /**
     * Display nearby peluquerías (por latitud/longitud y radio en km).
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'radio' => 'nullable|numeric|min:0',
        ]);

        $lat = $request->input('latitud');
        $lng = $request->input('longitud');
        $radius = $request->input('radio', 10);

        $distanceFormula = "(6371 * acos(cos(radians($lat)) * cos(radians(latitud)) * cos(radians(longitud) - radians($lng)) + sin(radians($lat)) * sin(radians(latitud))))";

        $peluquerias = Peluqueria::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->selectRaw("*, {$distanceFormula} as distancia")
            ->having('distancia', '<=', $radius)
            ->orderBy('distancia')
            ->with(['owner', 'peluqueros'])
            ->get();

        return response()->json([
            'peluquerias' => $peluquerias,
            'params' => ['latitud' => $lat, 'longitud' => $lng, 'radio' => $radius]
        ]);
    }

    /**
     * Display nearby peluqueros activos (por latitud/longitud y radio en km).
     */
    public function peluquerosNearby(Request $request)
    {
        $request->validate([
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'radio' => 'nullable|numeric|min:0',
        ]);

        $lat = $request->input('latitud');
        $lng = $request->input('longitud');
        $radius = $request->input('radio', 10);

        $distanceFormula = "(6371 * acos(cos(radians($lat)) * cos(radians(latitud)) * cos(radians(longitud) - radians($lng)) + sin(radians($lat)) * sin(radians(latitud))))";

        $peluqueros = \App\Models\User::where('role', 'peluquero')
            ->where('is_online', true)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->selectRaw("*, {$distanceFormula} as distancia")
            ->having('distancia', '<=', $radius)
            ->orderBy('distancia')
            ->get();

        return response()->json([
            'peluqueros' => $peluqueros,
            'params' => ['latitud' => $lat, 'longitud' => $lng, 'radio' => $radius]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isDueno() && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos para crear peluquerías'], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ]);

        $peluqueria = Peluqueria::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'owner_id' => $user->id,
        ]);

        return response()->json([
            'peluqueria' => $peluqueria->load(['owner', 'peluqueros']),
            'message' => 'Peluquería creada correctamente'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $peluqueria = Peluqueria::with(['owner', 'peluqueros', 'servicios'])->findOrFail($id);

        return response()->json([
            'peluqueria' => $peluqueria
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $peluqueria = Peluqueria::findOrFail($id);
        $user = Auth::user();

        // Solo el dueño de la peluquería puede editarla
        if ($peluqueria->owner_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos para editar esta peluquería'], 403);
        }

        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ]);

        $peluqueria->update($request->only([
            'nombre', 'descripcion', 'direccion', 'telefono', 'latitud', 'longitud'
        ]));

        return response()->json([
            'peluqueria' => $peluqueria->load(['owner', 'peluqueros']),
            'message' => 'Peluquería actualizada correctamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $peluqueria = Peluqueria::findOrFail($id);
        $user = Auth::user();

        // Solo el dueño de la peluquería puede eliminarla
        if ($peluqueria->owner_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos para eliminar esta peluquería'], 403);
        }

        $peluqueria->delete();

        return response()->json([
            'message' => 'Peluquería eliminada correctamente'
        ]);
    }

    /**
     * Agregar un peluquero a la peluquería
     */
    public function addPeluquero(Request $request, $id)
    {
        $peluqueria = Peluqueria::findOrFail($id);
        $user = Auth::user();

        // Solo el dueño puede agregar peluqueros
        if ($peluqueria->owner_id !== $user->id) {
            return response()->json(['message' => 'No tienes permisos para gestionar esta peluquería'], 403);
        }

        $request->validate([
            'peluquero_id' => 'required|exists:users,id',
        ]);

        $peluquero = \App\Models\User::where('id', $request->peluquero_id)
                                    ->where('role', 'peluquero')
                                    ->firstOrFail();

        if ($peluqueria->peluqueros()->where('user_id', $peluquero->id)->exists()) {
            return response()->json(['message' => 'Este peluquero ya está asignado a la peluquería'], 400);
        }

        $peluqueria->peluqueros()->attach($peluquero->id);

        return response()->json([
            'message' => 'Peluquero agregado correctamente',
            'peluqueria' => $peluqueria->load(['owner', 'peluqueros'])
        ]);
    }

    /**
     * Remover un peluquero de la peluquería
     */
    public function removePeluquero(Request $request, $id)
    {
        $peluqueria = Peluqueria::findOrFail($id);
        $user = Auth::user();

        // Solo el dueño puede remover peluqueros
        if ($peluqueria->owner_id !== $user->id) {
            return response()->json(['message' => 'No tienes permisos para gestionar esta peluquería'], 403);
        }

        $request->validate([
            'peluquero_id' => 'required|exists:users,id',
        ]);

        $peluqueria->peluqueros()->detach($request->peluquero_id);

        return response()->json([
            'message' => 'Peluquero removido correctamente',
            'peluqueria' => $peluqueria->load(['owner', 'peluqueros'])
        ]);
    }
}