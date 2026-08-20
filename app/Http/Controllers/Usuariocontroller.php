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
        return Rol::whereIn('descripcion', ['admin', 'vendedor', 'Administrador', 'Empleado'])
            ->orderBy('descripcion')
            ->get();
    }

    /**
     * Listar usuarios internos (Administrador y Empleado, rol_id 1 y 2).
     * Los Clientes (rol_id 3) no se gestionan aquí.
     */
    public function index(): View //la funcion index es para mostrar todos los usuarios de la tabla usuarios
    {
        $users = User::whereIn('rol_id', [1, 2])
            ->with('rol') //se incluye el modelo rol para obtener el nombre del rol
            ->orderByDesc('fecha_registro') //se ordenan los usuarios por fecha de registro en orden descendente
            ->get(); //se obtiene todos los usuarios  

        $roles = $this->rolesAsignables(); //se obtiene todos los roles asignables

        return view('admin.usuarios', compact('users', 'roles')); //se retorna la vista admin.usuarios con los usuarios y roles
    }

    /**
     * Crear un nuevo usuario interno (Administrador o Empleado, según se elija).
     */
    public function store(Request $request): RedirectResponse //la funcion store es para crear un nuevo usuario
    {
        $rolesPermitidos = $this->rolesAsignables()->pluck('id'); //se obtienen los roles asignables

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

        User::create([ // esta funcion es para guardar los datos del nuevo usuario en la tabla usuarios
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'username' => $request->username,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password), //se hashea la contraseña
            'rol_id' => $request->rol_id, //se asigna el rol
            'estado' => $request->estado, //se asigna el estado
        ]);

        return redirect()->route('usuarios.index')
            ->with('toast', ['type' => 'success', 'text' => 'Usuario creado correctamente.']);
    }

    /**
     * Actualizar un usuario interno existente.
     * La contraseña solo se actualiza si se envía un valor nuevo.
     */
    public function update(Request $request, User $usuario): RedirectResponse //la funcion update es para actualizar los datos de un usuario existente
    {
        $rolesPermitidos = $this->rolesAsignables()->pluck('id'); //se obtienen los roles asignables

        $request->validate([
            'nombre' => ['required', 'string', 'max:100'], //se valida el nombre
            'apellido' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($usuario->id)],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($usuario->id)],
            'telefono' => ['nullable', 'string', 'max:15'],
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'rol_id' => ['required', Rule::in($rolesPermitidos)],
        ]);

        $usuario->nombre = $request->nombre; //se actualiza el nombre
        $usuario->apellido = $request->apellido; //se actualiza el apellido
        $usuario->username = $request->username; //se actualiza el username
        $usuario->email = $request->email; //se actualiza el email
        $usuario->telefono = $request->telefono; //se actualiza el telefono
        $usuario->estado = $request->estado; //se actualiza el estado
        $usuario->rol_id = $request->rol_id; //se actualiza el rol

        if ($request->filled('password')) { //se hashea la contraseña si se envia un valor nuevo
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')
            ->with('toast', ['type' => 'success', 'text' => 'Usuario actualizado correctamente.']);
    }

    /**
     * Activar / desactivar un usuario (llamado vía fetch desde la tabla). */
    public function toggleEstado(Request $request, User $usuario): JsonResponse //la funcion toggleEstado es para activar o desactivar un usuario
    {
        $request->validate([
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])], //se valida el estado
        ]);

        $usuario->update(['estado' => $request->estado]); //se actualiza el estado del usuario

        return response()->json(['ok' => true, 'estado' => $usuario->estado]); //se retorna el estado del usuario
    }
}