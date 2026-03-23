<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CitaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'cliente' => new UserResource($this->whenLoaded('cliente')),
            'peluquero' => new UserResource($this->whenLoaded('peluquero')),
            'peluqueria' => new PeluqueriaResource($this->whenLoaded('peluqueria')),
            'servicio' => new ServicioResource($this->whenLoaded('servicio')),
            'fecha' => $this->fecha?->toDateString(),
            'hora' => $this->hora,
            'estado' => $this->estado,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
