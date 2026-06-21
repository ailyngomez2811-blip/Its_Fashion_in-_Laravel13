<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Roles que se pueden asignar desde este panel (no incluye Cliente,
     * los clientes se registran por su cuenta desde /register).
     */
    private function rolesAsignables()
    {
        return Rol::whereIn('descripcion', ['Administrador', 'Empleado'])
            ->orderBy('descripcion')
            ->get();
    }

    /**
     * Listar usuarios internos (Administrador y Empleado, rol_id 1 y 2).
     * Los Clientes (rol_id 3) no se gestionan aquí.
     */
    public function index(): View
    {
        $users = User::whereIn('rol_id', [1, 2])
            ->with('rol')
            ->orderByDesc('fecha_registro')
            ->get();

        $roles = $this->rolesAsignables();

        return view('admin.usuarios', compact('users', 'roles'));
    }

    /**
     * Crear un nuevo usuario interno (Administrador o Empleado, según se elija).
     */
    public function store(Request $request): RedirectResponse
    {
        $rolesPermitidos = $this->rolesAsignables()->pluck('id');

        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'rol_id' => ['required', Rule::in($rolesPermitidos)],
        ]);

        User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'username' => $request->username,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'estado' => $request->estado,
        ]);

        return redirect()->route('usuarios.index')
            ->with('toast', ['type' => 'success', 'text' => 'Usuario creado correctamente.']);
    }

    /**
     * Actualizar un usuario interno existente.
     * La contraseña solo se actualiza si se envía un valor nuevo.
     */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        $rolesPermitidos = $this->rolesAsignables()->pluck('id');

        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($usuario->id)],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($usuario->id)],
            'telefono' => ['nullable', 'string', 'max:15'],
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'rol_id' => ['required', Rule::in($rolesPermitidos)],
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->apellido = $request->apellido;
        $usuario->username = $request->username;
        $usuario->email = $request->email;
        $usuario->telefono = $request->telefono;
        $usuario->estado = $request->estado;
        $usuario->rol_id = $request->rol_id;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')
            ->with('toast', ['type' => 'success', 'text' => 'Usuario actualizado correctamente.']);
    }

    /**
     * Activar / desactivar un usuario (llamado vía fetch desde la tabla).
     */
    public function toggleEstado(Request $request, User $usuario): JsonResponse
    {
        $request->validate([
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ]);

        $usuario->update(['estado' => $request->estado]);

        return response()->json(['ok' => true, 'estado' => $usuario->estado]);
    }
}