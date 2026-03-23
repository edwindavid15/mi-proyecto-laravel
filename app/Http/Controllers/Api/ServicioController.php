<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServicioRequest;
use App\Http\Resources\ServicioResource;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isPeluquero()) {
            $servicios = Servicio::where('peluquero_id', $user->id)->with(['peluqueria', 'peluquero'])->get();
        } elseif ($user->isDueno()) {
            $servicios = Servicio::whereIn('peluqueria_id', $user->peluquerias->pluck('id'))->with(['peluqueria', 'peluquero'])->get();
        } else {
            $servicios = Servicio::with(['peluqueria', 'peluquero'])->get();
        }

        return ServicioResource::collection($servicios);
    }

    public function store(ServicioRequest $request)
    {
        $user = Auth::user();

        $data = $request->validatedData();
        $data['peluquero_id'] = $user->id;

        if ($user->isDueno() && !empty($data['peluqueria_id'])) {
            if (! $user->peluquerias()->where('id', $data['peluqueria_id'])->exists()) {
                return response()->json(['message' => 'No tienes permisos sobre esta peluquería'], 403);
            }
        }

        $servicio = Servicio::create($data);

        return response()->json([
            'servicio' => new ServicioResource($servicio->load(['peluqueria', 'peluquero'])),
            'message' => 'Servicio creado correctamente'
        ], 201);
    }

    public function show(Servicio $servicio)
    {
        return new ServicioResource($servicio->load(['peluqueria', 'peluquero']));
    }

    public function update(ServicioRequest $request, Servicio $servicio)
    {
        $user = Auth::user();

        if ($servicio->peluquero_id !== $user->id && !$user->isDueno() && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos para editar este servicio'], 403);
        }

        if ($user->isDueno() && ! $user->peluquerias()->where('id', $servicio->peluqueria_id)->exists()) {
            return response()->json(['message' => 'No tienes permisos sobre este servicio'], 403);
        }

        $servicio->update($request->validatedData());

        return response()->json([
            'servicio' => new ServicioResource($servicio->load(['peluqueria', 'peluquero'])),
            'message' => 'Servicio actualizado correctamente'
        ]);
    }

    public function destroy(Servicio $servicio)
    {
        $user = Auth::user();

        if ($servicio->peluquero_id !== $user->id && !$user->isDueno() && !$user->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos para eliminar este servicio'], 403);
        }

        if ($user->isDueno() && ! $user->peluquerias()->where('id', $servicio->peluqueria_id)->exists()) {
            return response()->json(['message' => 'No tienes permisos sobre este servicio'], 403);
        }

        $servicio->delete();

        return response()->json(['message' => 'Servicio eliminado correctamente']);
    }
}
