<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PeluqueriaRequest;
use App\Http\Resources\PeluqueriaResource;
use App\Models\Peluqueria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeluqueriaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isDueno()) {
            $peluquerias = $user->ownedPeluquerias()->with(['owner', 'peluqueros', 'servicios'])->get();
        } elseif ($user->isPeluquero()) {
            $peluquerias = $user->peluquerias()->with(['owner', 'peluqueros', 'servicios'])->get();
        } else {
            $peluquerias = Peluqueria::where('is_active', true)->with(['owner', 'peluqueros', 'servicios'])->get();
        }

        return PeluqueriaResource::collection($peluquerias);
    }

    public function store(PeluqueriaRequest $request)
    {
        $user = Auth::user();

        $peluqueria = Peluqueria::create(array_merge(
            $request->validatedData(),
            ['owner_id' => $user->id]
        ));

        return response()->json([
            'peluqueria' => new PeluqueriaResource($peluqueria->load(['owner', 'peluqueros', 'servicios'])),
            'message' => 'Peluquería creada correctamente'
        ], 201);
    }

    public function show(Peluqueria $peluqueria)
    {
        return new PeluqueriaResource($peluqueria->load(['owner', 'peluqueros', 'servicios']));
    }

    public function update(PeluqueriaRequest $request, Peluqueria $peluqueria)
    {
        $user = Auth::user();

        if ($peluqueria->owner_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos para editar esta peluquería'], 403);
        }

        $peluqueria->update($request->validatedData());

        return response()->json([
            'peluqueria' => new PeluqueriaResource($peluqueria->load(['owner', 'peluqueros', 'servicios'])),
            'message' => 'Peluquería actualizada correctamente'
        ]);
    }

    public function destroy(Peluqueria $peluqueria)
    {
        $user = Auth::user();

        if ($peluqueria->owner_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos para eliminar esta peluquería'], 403);
        }

        $peluqueria->delete();

        return response()->json(['message' => 'Peluquería eliminada correctamente']);
    }

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

        $peluquerias = Peluqueria::where('is_active', true)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->selectRaw("*, {$distanceFormula} as distancia")
            ->having('distancia', '<=', $radius)
            ->orderBy('distancia')
            ->with(['owner', 'peluqueros', 'servicios'])
            ->get();

        return response()->json(['peluquerias' => PeluqueriaResource::collection($peluquerias)]);
    }

    public function addPeluquero(Request $request, Peluqueria $peluqueria)
    {
        $user = Auth::user();

        if ($peluqueria->owner_id !== $user->id) {
            return response()->json(['message' => 'No tienes permisos para gestionar esta peluquería'], 403);
        }

        $request->validate(['peluquero_id' => 'required|exists:users,id']);

        $peluquero = \App\Models\User::where('id', $request->peluquero_id)
            ->where('role', 'peluquero')
            ->firstOrFail();

        if ($peluqueria->peluqueros()->where('user_id', $peluquero->id)->exists()) {
            return response()->json(['message' => 'Este peluquero ya está asignado a la peluquería'], 400);
        }

        $peluqueria->peluqueros()->attach($peluquero->id);

        return response()->json([
            'message' => 'Peluquero agregado correctamente',
            'peluqueria' => new PeluqueriaResource($peluqueria->load(['owner', 'peluqueros', 'servicios']))
        ]);
    }

    public function removePeluquero(Request $request, Peluqueria $peluqueria)
    {
        $user = Auth::user();

        if ($peluqueria->owner_id !== $user->id) {
            return response()->json(['message' => 'No tienes permisos para gestionar esta peluquería'], 403);
        }

        $request->validate(['peluquero_id' => 'required|exists:users,id']);

        $peluqueria->peluqueros()->detach($request->peluquero_id);

        return response()->json([
            'message' => 'Peluquero removido correctamente',
            'peluqueria' => new PeluqueriaResource($peluqueria->load(['owner', 'peluqueros', 'servicios']))
        ]);
    }
}
