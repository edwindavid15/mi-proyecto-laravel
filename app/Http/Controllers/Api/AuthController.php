<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', User::ROLES),
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos de registro inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
                'is_active' => true,
            ]);

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos de login inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Cuenta desactivada'
            ], 401);
        }

        // Revocar tokens anteriores para mayor seguridad
        $user->tokens()->delete();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'user' => $user->load(['peluquerias', 'ownedPeluquerias']),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Sesión cerrada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->user()->load([
                'peluquerias',
                'ownedPeluquerias',
                'servicios',
                'citasComoCliente.activas',
                'citasComoPeluquero.activas'
            ]);

            return response()->json([
                'user' => $user,
                'stats' => [
                    'total_citas_cliente' => $user->citasComoCliente()->count(),
                    'citas_activas_cliente' => $user->citasComoCliente()->activas()->count(),
                    'total_citas_peluquero' => $user->citasComoPeluquero()->count(),
                    'citas_activas_peluquero' => $user->citasComoPeluquero()->activas()->count(),
                    'total_servicios' => $user->servicios()->count(),
                    'total_peluquerias_propias' => $user->ownedPeluquerias()->count(),
                    'total_peluquerias_trabaja' => $user->peluquerias()->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'required_with:password|string',
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar contraseña actual si se quiere cambiar la contraseña
        if ($request->has('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Contraseña actual incorrecta'
                ], 400);
            }
            $user->password = Hash::make($request->password);
        }

        try {
            $user->update($request->only(['name', 'email', 'phone']));

            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'user' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deactivateAccount(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Contraseña incorrecta'
            ], 400);
        }

        try {
            // Cancelar citas activas
            $user->citasComoCliente()->activas()->update(['estado' => 'cancelada']);

            // Desactivar cuenta
            $user->update(['is_active' => false]);

            // Revocar tokens
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Cuenta desactivada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al desactivar cuenta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}