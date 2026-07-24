<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;


class ProveedorController extends Controller
{
    /**
     * Muestra la lista de proveedores.
     */
    public function index(): View
    {
        // Se obtienen todos los proveedores ordenados por nombre alfabéticamente
        $proveedores = Proveedor::orderBy('nombre', 'asc')->get();

        return view('admin.proveedores', compact('proveedores'));
    }

    /**
     * Almacena un nuevo proveedor en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'contacto' => ['required', 'string', 'max:100'],
            'telefono' => ['required', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'documento' => ['nullable', 'string', 'max:20', 'unique:proveedores,documento'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'contacto.required' => 'El nombre del contacto es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'documento.unique' => 'Ya existe un proveedor registrado con ese documento.',
            'email.email' => 'Por favor introduce un correo electrónico válido.',
        ]);

        Proveedor::create([
            'nombre' => trim($request->nombre),
            'contacto' => trim($request->contacto),
            'telefono' => trim($request->telefono),
            'email' => $request->email ? trim($request->email) : null,
            'direccion' => $request->direccion ? trim($request->direccion) : null,
            'documento' => $request->documento ? trim($request->documento) : null,
        ]);

        return redirect()->route('proveedores.index')
            ->with('toast', [
                'type' => 'success',
                'text' => 'Proveedor creado correctamente.'
            ]);
    }

    /**
     * Muestra los detalles de un proveedor específico en formato JSON (edición AJAX).
     */
    public function show(Proveedor $proveedor): JsonResponse
    {
        return response()->json($proveedor);
    }

    /**
     * Actualiza un proveedor existente.
     */
    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'contacto' => ['required', 'string', 'max:100'],
            'telefono' => ['required', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'documento' => ['nullable', 'string', 'max:20', Rule::unique('proveedores', 'documento')->ignore($proveedor->id)],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'contacto.required' => 'El nombre del contacto es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'documento.unique' => 'Ya existe un proveedor registrado con ese documento.',
            'email.email' => 'Por favor introduce un correo electrónico válido.',
        ]);

        $proveedor->update([
            'nombre' => trim($request->nombre),
            'contacto' => trim($request->contacto),
            'telefono' => trim($request->telefono),
            'email' => $request->email ? trim($request->email) : null,
            'direccion' => $request->direccion ? trim($request->direccion) : null,
            'documento' => $request->documento ? trim($request->documento) : null,
        ]);

        return redirect()->route('proveedores.index')
            ->with('toast', [
                'type' => 'success',
                'text' => 'Proveedor actualizado correctamente.'
            ]);
    }

    /**
     * Elimina un proveedor si no tiene compras asociadas.
     */
    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        // Verificar si el proveedor tiene compras asociadas
        $tieneCompras = DB::table('compras')
            ->where('proveedor_id', $proveedor->id)
            ->exists();

        if ($tieneCompras) {
            return redirect()->route('proveedores.index')
                ->with('toast', [
                    'type' => 'error',
                    'text' => 'No se puede eliminar el proveedor: tiene compras asociadas.'
                ]);
        }

        // Eliminar proveedor
        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('toast', [
                'type' => 'success',
                'text' => 'Proveedor eliminado correctamente.'
            ]);
    }
}
