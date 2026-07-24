<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    /**
     * Muestra la lista de categorías.
     */
    public function index(): View
    {
        // Se obtienen todas las categorías ordenadas alfabéticamente por nombre
        $categorias = Categoria::orderBy('nombre', 'asc')->get();

        // Retorna la vista en resources/views/admin/categorias.blade.php
        return view('admin.categorias', compact('categorias'));
    }

    /**
     * Almacena una nueva categoría.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación de datos basados en la lógica original
        $request->validate([
            'nombre' => [
                'required', 
                'string', 
                'max:50', 
                'unique:categorias,nombre'
            ],
            'descripcion' => [
                'nullable', 
                'string'
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        // Creación del registro
        Categoria::create([
            'nombre' => trim($request->nombre),
            'descripcion' => trim($request->descripcion),
        ]);

        // Redirección con mensaje Toast (tipo success)
        return redirect()->route('categorias.index')
            ->with('toast', [
                'type' => 'success', 
                'text' => 'Categoría creada correctamente.'
            ]);
    }

    /**
     * Actualiza una categoría existente.
     */
    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        // Validación que ignora el registro actual para el campo único 'nombre'
        $request->validate([
            'nombre' => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('categorias', 'nombre')->ignore($categoria->id)
            ],
            'descripcion' => [
                'nullable', 
                'string'
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        // Actualización del registro
        $categoria->update([
            'nombre' => trim($request->nombre),
            'descripcion' => trim($request->descripcion),
        ]);

        return redirect()->route('categorias.index')
            ->with('toast', [
                'type' => 'success', 
                'text' => 'Categoría actualizada correctamente.'
            ]);
    }

    /**
     * Elimina una categoría si no tiene productos asociados.
     */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        // Se verifica si existen productos asociados usando Query Builder para evitar problemas de dependencia del modelo Producto
        $tieneProductos = DB::table('productos')
            ->where('categoria_id', $categoria->id)
            ->exists();

        if ($tieneProductos) {
            // Si tiene productos asociados, no se permite eliminar y se envía toast tipo error
            return redirect()->route('categorias.index')
                ->with('toast', [
                    'type' => 'error', 
                    'text' => 'No se puede eliminar: tiene productos asociados.'
                ]);
        }

        // Se elimina la categoría
        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('toast', [
                'type' => 'success', 
                'text' => 'Categoría eliminada correctamente.'
            ]);
    }
}
