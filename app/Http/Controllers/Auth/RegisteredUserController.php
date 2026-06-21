<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            // "documento" llega del formulario, pero se guarda en la columna "username"
            'documento' => 'required|string|max:20|unique:users,username',
            'telefono' => 'required|string|max:15',
            'email' => 'required|string|lowercase|email|max:100|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // ROL POR DEFECTO PARA REGISTRO PÚBLICO: CLIENTE
        $rolCliente = Rol::where('descripcion', 'Cliente')->first();

        $user = User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'username' => $request->documento,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $rolCliente?->id,
        ]);

        event(new Registered($user));

        // NO se autentica automáticamente: el usuario debe iniciar sesión manualmente.
        return redirect()->route('login')
            ->with('status', '¡Registro exitoso! Ya puedes iniciar sesión.');
    }
}
