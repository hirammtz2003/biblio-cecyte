<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $usuario = Auth::user();
        return view('profile.show', compact('usuario'));
    }

    public function edit()
    {
        $usuario = Auth::user();
        return view('profile.edit', compact('usuario'));
    }

    public function update(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('usuarios')->ignore($usuario->id)
            ],
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ], [
            'current_password.required_with' => 'La contraseña actual es requerida para cambiar la contraseña.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ]);

        // Verificar contraseña actual si se quiere cambiar la contraseña
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $usuario->contrasena)) {
                return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
            }
        }

        // Actualizar datos
        $usuario->update([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'contrasena' => $request->filled('new_password') 
                ? Hash::make($request->new_password) 
                : $usuario->contrasena,
        ]);

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado correctamente.');
    }


    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (Hash::check($request->password, Auth::user()->contrasena)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
    }
}