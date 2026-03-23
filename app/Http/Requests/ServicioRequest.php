<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && ($user->isPeluquero() || $user->isDueno() || $user->isAdmin());
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0',
            'duracion' => 'required|integer|min:1',
            'peluqueria_id' => 'nullable|exists:peluquerias,id',
        ];
    }

    public function validatedData(): array
    {
        return $this->safe()->only(['nombre', 'descripcion', 'precio', 'duracion', 'peluqueria_id']);
    }
}
