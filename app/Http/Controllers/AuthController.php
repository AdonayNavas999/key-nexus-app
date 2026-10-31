<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name'     => 'required|string|min:3|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/',
                'confirmed',
            ],
        ], [
            'name.required'     => 'El nombre es obligatorio.',
            'name.string'       => 'El nombre debe ser una cadena de texto.',
            'name.min'          => 'El nombre debe tener al menos 3 caracteres.',
            'name.max'          => 'El nombre no debe superar los 255 caracteres.',
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.string'      => 'El correo electrónico debe ser una cadena de texto.',
            'email.email'       => 'Introduce un correo electrónico válido.',
            'email.max'         => 'El correo electrónico no debe superar los 255 caracteres.',
            'email.unique'      => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string'   => 'La contraseña debe ser una cadena de texto.',
            'password.min'      => 'La contraseña debe tener al menos 12 caracteres.',
            'password.regex'    => 'La contraseña debe tener al menos 12 caracteres e incluir una minúscula, una mayúscula, un número y un símbolo (@$!%*?&). Solo se permiten letras sin acentos, números y esos símbolos.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = User::create([
            'name'     => $validatedData['name'],
            'email'    => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Emitir token inicial al registrarse
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Usuario registrado exitosamente',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], 201);
    }

    // Inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Introduce un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string'   => 'La contraseña debe ser una cadena de texto.',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Generar nuevo token Bearer
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], 200);
    }

    // Cierre de sesión (Revocar token actual)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ], 200);
    }

    // Obtener información del usuario autenticado
    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }
}
