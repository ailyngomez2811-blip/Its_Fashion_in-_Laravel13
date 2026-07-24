<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\InventarioController;
use App\Models\Producto;








Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    /** @var User $user */
    $user = Auth::user();

    return match ((int) $user->rol_id) {
        1 => view('admin.dashboard', [
            'totalClientes' => User::where('rol_id', 3)->count(),
            'clientesActivos' => User::where('rol_id', 3)->where('estado', 'Activo')->count(),
            'totalProductos' => Producto::count(),

        ]),
        2 => view('empleado.dashboard'),
        3 => view('cliente.dashboard'),
        default => abort(403),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/toggle-estado', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle-estado');
});

Route::resource('categorias', CategoriaController::class)
    ->middleware('auth');

Route::resource('productos', ProductoController::class)
    ->middleware('auth');

Route::patch('/productos/{producto}/toggle-estado', [ProductoController::class, 'toggleEstado'])
    ->middleware('auth')
    ->name('productos.toggle-estado');

Route::resource('proveedores', ProveedorController::class)
    ->middleware('auth');

Route::resource('compras', CompraController::class)
    ->middleware('auth');

Route::get('/ventas/buscar-cliente', [VentaController::class, 'buscarCliente'])
    ->middleware('auth')
    ->name('ventas.buscar-cliente');

Route::resource('ventas', VentaController::class)
    ->middleware('auth');

Route::post('/devoluciones/{devolucion}/aprobar', [DevolucionController::class, 'aprobar'])
    ->middleware('auth')
    ->name('devoluciones.aprobar');

Route::post('/devoluciones/{devolucion}/rechazar', [DevolucionController::class, 'rechazar'])
    ->middleware('auth')
    ->name('devoluciones.rechazar');

Route::resource('devoluciones', DevolucionController::class)
    ->middleware('auth');

Route::get('/caja', [CajaController::class, 'index'])
    ->middleware('auth')
    ->name('caja.index');

Route::post('/caja/abrir', [CajaController::class, 'abrir'])
    ->middleware('auth')
    ->name('caja.abrir');

Route::post('/caja/cerrar', [CajaController::class, 'cerrar'])
    ->middleware('auth')
    ->name('caja.cerrar');

Route::post('/caja/movimiento', [CajaController::class, 'registrarMovimiento'])
    ->middleware('auth')
    ->name('caja.movimiento');

Route::get('/inventario', [InventarioController::class, 'index'])
    ->middleware('auth')
    ->name('inventario.index');







