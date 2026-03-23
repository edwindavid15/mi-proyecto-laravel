<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PeluqueriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();

        // Solo dueños y admins pueden crear/actualizar datos comerciales / de ubicación.
        return $user && ($user->isDueno() || $user->isAdmin());
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'direccion' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function validatedData(): array
    {
        return $this->safe()->only(['nombre', 'descripcion', 'direccion', 'telefono', 'latitud', 'longitud', 'is_active']);
    }
}
