<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use App\Models\Inventario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles por defecto
        Rol::create(['id' => 1, 'descripcion' => 'admin']);
        Rol::create(['id' => 2, 'descripcion' => 'vendedor']);
        Rol::create(['id' => 3, 'descripcion' => 'cliente']);

        // Crear un usuario administrador por defecto
        $admin = User::create([
            'nombre' => 'Admin',
            'apellido' => 'General',
            'username' => 'admin',
            'telefono' => '123456789',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'rol_id' => 1,
            'estado' => 'Activo',
        ]);

        // Crear un usuario vendedor por defecto
        $vendedor = User::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'username' => 'juanvendedor',
            'telefono' => '987654321',
            'email' => 'juan@gmail.com',
            'password' => Hash::make('vendedor123'),
            'rol_id' => 2,
            'estado' => 'Activo',
        ]);

        // Crear un usuario cliente por defecto
        $cliente = User::create([
            'nombre' => 'Ailyn',
            'apellido' => 'Gomez',
            'username' => 'ailyncliente',
            'telefono' => '3223945112',
            'email' => 'ailyngomez.2811@gmail.com',
            'password' => Hash::make('cliente123'),
            'rol_id' => 3,
            'estado' => 'Activo',
        ]);

        // 1. CATEGORÍAS (De Ropa y Accesorios)
        $catJeans = \App\Models\Categoria::create([
            'nombre' => 'Jeans',
            'descripcion' => 'Jeans de hombre, mujer y unisex de diversos estilos.'
        ]);

        $catCamisetas = \App\Models\Categoria::create([
            'nombre' => 'Camisetas y Tops',
            'descripcion' => 'Camisetas básicas, estampadas y tops de verano.'
        ]);

        $catVestidos = \App\Models\Categoria::create([
            'nombre' => 'Vestidos',
            'descripcion' => 'Vestidos casuales, de gala y enterizos.'
        ]);

        $catChaquetas = \App\Models\Categoria::create([
            'nombre' => 'Chaquetas y Abrigos',
            'descripcion' => 'Prendas de abrigo y chaquetas de cuero o denim.'
        ]);

        $catCalzado = \App\Models\Categoria::create([
            'nombre' => 'Calzado',
            'descripcion' => 'Zapatos, zapatillas deportivas, botas y sandalias.'
        ]);

        $catAccesorios = \App\Models\Categoria::create([
            'nombre' => 'Accesorios',
            'descripcion' => 'Bolsos, cinturones, gafas de sol y sombreros.'
        ]);

        // 2. PRODUCTOS
        $prodJeansSlim = \App\Models\Producto::create([
            'nombre' => 'Jeans Slim Fit Azul',
            'descripcion' => 'Jeans ajustados de denim elástico para mujer.',
            'precio_compra' => 45000.00,
            'precio_venta' => 85000.00,
            'stock' => 50,
            'stock_minimo' => 5,
            'talla' => 'M',
            'color' => 'Azul Oscuro',
            'estado' => 'Activo',
            'categoria_id' => $catJeans->id
        ]);

        $prodCamisetaAlgodon = \App\Models\Producto::create([
            'nombre' => 'Camiseta Básica de Algodón',
            'descripcion' => 'Camiseta de algodón 100% orgánico cuello redondo.',
            'precio_compra' => 15000.00,
            'precio_venta' => 35000.00,
            'stock' => 100,
            'stock_minimo' => 10,
            'talla' => 'S',
            'color' => 'Blanco',
            'estado' => 'Activo',
            'categoria_id' => $catCamisetas->id
        ]);

        $prodVestidoFloral = \App\Models\Producto::create([
            'nombre' => 'Vestido Floral de Verano',
            'descripcion' => 'Vestido corto con estampado floral y tirantes ajustables.',
            'precio_compra' => 50000.00,
            'precio_venta' => 98000.00,
            'stock' => 30,
            'stock_minimo' => 3,
            'talla' => 'M',
            'color' => 'Rojo',
            'estado' => 'Activo',
            'categoria_id' => $catVestidos->id
        ]);

        $prodChaquetaCuero = \App\Models\Producto::create([
            'nombre' => 'Chaqueta de Cuero Biker',
            'descripcion' => 'Chaqueta estilo moto de cuero sintético con cremalleras.',
            'precio_compra' => 90000.00,
            'precio_venta' => 180000.00,
            'stock' => 20,
            'stock_minimo' => 2,
            'talla' => 'L',
            'color' => 'Negro',
            'estado' => 'Activo',
            'categoria_id' => $catChaquetas->id
        ]);

        $prodTenisRunning = \App\Models\Producto::create([
            'nombre' => 'Tenis Deportivos Running',
            'descripcion' => 'Zapatillas ligeras de correr con suela amortiguadora.',
            'precio_compra' => 70000.00,
            'precio_venta' => 145000.00,
            'stock' => 40,
            'stock_minimo' => 4,
            'talla' => '38',
            'color' => 'Blanco/Azul',
            'estado' => 'Activo',
            'categoria_id' => $catCalzado->id
        ]);

        $prodBotasCuero = \App\Models\Producto::create([
            'nombre' => 'Botas de Cuero Casuales',
            'descripcion' => 'Botas de cuero genuino caña media con cordones.',
            'precio_compra' => 120000.00,
            'precio_venta' => 230000.00,
            'stock' => 15,
            'stock_minimo' => 2,
            'talla' => '40',
            'color' => 'Café',
            'estado' => 'Activo',
            'categoria_id' => $catCalzado->id
        ]);

        $prodGafasRetro = \App\Models\Producto::create([
            'nombre' => 'Gafas de Sol Retro',
            'descripcion' => 'Gafas de sol con protección UV400 y montura de carey.',
            'precio_compra' => 20000.00,
            'precio_venta' => 45000.00,
            'stock' => 60,
            'stock_minimo' => 5,
            'talla' => 'Única',
            'color' => 'Marrón',
            'estado' => 'Activo',
            'categoria_id' => $catAccesorios->id
        ]);

        $prodCinturonCuero = \App\Models\Producto::create([
            'nombre' => 'Cinturón de Cuero Reversible',
            'descripcion' => 'Cinturón elegante de cuero reversible con hebilla metálica.',
            'precio_compra' => 18000.00,
            'precio_venta' => 40000.00,
            'stock' => 80,
            'stock_minimo' => 5,
            'talla' => '32',
            'color' => 'Negro/Marrón',
            'estado' => 'Activo',
            'categoria_id' => $catAccesorios->id
        ]);

        // 3. PROVEEDORES
        $provTextiles = \App\Models\Proveedor::create([
            'nombre' => 'Textiles y Confecciones del Norte',
            'contacto' => 'Carlos Mendoza',
            'telefono' => '3157894561',
            'email' => 'contacto@textilesnorte.com',
            'direccion' => 'Calle 45 #12-34, Bogotá',
            'documento' => 'NIT-900876543-1'
        ]);

        $provModaDistribucion = \App\Models\Proveedor::create([
            'nombre' => 'Moda & Distribución Express',
            'contacto' => 'Lucía Restrepo',
            'telefono' => '3206549872',
            'email' => 'ventas@modadistribucion.com',
            'direccion' => 'Av. El Poblado #5-22, Medellín',
            'documento' => 'NIT-800432109-2'
        ]);

        $provCalzadoSur = \App\Models\Proveedor::create([
            'nombre' => 'Calzado y Accesorios del Sur',
            'contacto' => 'Andrés Beltrán',
            'telefono' => '3189998883',
            'email' => 'contacto@calzadosur.com',
            'direccion' => 'Calle 10 #20-50, Cali',
            'documento' => 'NIT-700999888-3'
        ]);

        $provImportModa = \App\Models\Proveedor::create([
            'nombre' => 'Importaciones y Moda SAS',
            'contacto' => 'Gabriela Silva',
            'telefono' => '3117771114',
            'email' => 'importaciones@modasas.com',
            'direccion' => 'Zona Franca Bodega 4, Barranquilla',
            'documento' => 'NIT-600777111-4'
        ]);

        // 4. COMPRAS (Registro de abastecimiento de stock)
        // Compra 1 realizada por el Administrador al proveedor Textiles del Norte
        $compra1 = \App\Models\Compra::create([
            'fecha' => now()->subDays(5),
            'total' => 2250000.00, // 50 Jeans * 45000 = 2,250,000
            'proveedor_id' => $provTextiles->id,
            'usuario_id' => $admin->id
        ]);

        \App\Models\DetalleCompra::create([
            'compra_id' => $compra1->id,
            'producto_id' => $prodJeansSlim->id,
            'cantidad' => 50,
            'precio_unitario' => 45000.00,
            'subtotal' => 2250000.00
        ]);

        // Compra 2 realizada por el Administrador al proveedor Moda & Distribución
        $compra2 = \App\Models\Compra::create([
            'fecha' => now()->subDays(2),
            'total' => 3300000.00, // 100 Camisetas * 15000 = 1,500,000 + 20 Chaquetas * 90000 = 1,800,000
            'proveedor_id' => $provModaDistribucion->id,
            'usuario_id' => $admin->id
        ]);

        \App\Models\DetalleCompra::create([
            'compra_id' => $compra2->id,
            'producto_id' => $prodCamisetaAlgodon->id,
            'cantidad' => 100,
            'precio_unitario' => 15000.00,
            'subtotal' => 1500000.00
        ]);

        \App\Models\DetalleCompra::create([
            'compra_id' => $compra2->id,
            'producto_id' => $prodChaquetaCuero->id,
            'cantidad' => 20,
            'precio_unitario' => 90000.00,
            'subtotal' => 1800000.00
        ]);

        // Compra 3 realizada por el Administrador al proveedor Calzado del Sur
        $compra3 = \App\Models\Compra::create([
            'fecha' => now()->subDays(1),
            'total' => 4600000.00, // 40 Tenis * 70000 = 2,800,000 + 15 Botas * 120000 = 1,800,000
            'proveedor_id' => $provCalzadoSur->id,
            'usuario_id' => $admin->id
        ]);

        \App\Models\DetalleCompra::create([
            'compra_id' => $compra3->id,
            'producto_id' => $prodTenisRunning->id,
            'cantidad' => 40,
            'precio_unitario' => 70000.00,
            'subtotal' => 2800000.00
        ]);

        \App\Models\DetalleCompra::create([
            'compra_id' => $compra3->id,
            'producto_id' => $prodBotasCuero->id,
            'cantidad' => 15,
            'precio_unitario' => 120000.00,
            'subtotal' => 1800000.00
        ]);

        // 5. VENTAS (Ventas simuladas al cliente por defecto)
        // Venta 1: Cliente compra 1 Jeans Slim Fit y 1 Camiseta Básica
        $venta1 = \App\Models\Venta::create([
            'fecha' => now()->subDays(4),
            'total' => 120000.00,
            'cliente_id' => $cliente->id,
            'metodo_pago' => 'Efectivo',
            'estado' => 'Completada',
            'usuario_id' => $vendedor->id
        ]);

        \App\Models\DetalleVenta::create([
            'venta_id' => $venta1->id,
            'producto_id' => $prodJeansSlim->id,
            'cantidad' => 1,
            'precio_unitario' => 85000.00
        ]);

        \App\Models\DetalleVenta::create([
            'venta_id' => $venta1->id,
            'producto_id' => $prodCamisetaAlgodon->id,
            'cantidad' => 1,
            'precio_unitario' => 35000.00
        ]);

        // Venta 2: Cliente compra 1 Chaqueta de cuero
        $venta2 = \App\Models\Venta::create([
            'fecha' => now()->subDays(3),
            'total' => 180000.00,
            'cliente_id' => $cliente->id,
            'metodo_pago' => 'Transferencia bancaria',
            'estado' => 'Completada',
            'usuario_id' => $vendedor->id
        ]);

        \App\Models\DetalleVenta::create([
            'venta_id' => $venta2->id,
            'producto_id' => $prodChaquetaCuero->id,
            'cantidad' => 1,
            'precio_unitario' => 180000.00
        ]);

        // Venta 3: Cliente compra Tenis y Gafas
        $venta3 = \App\Models\Venta::create([
            'fecha' => now()->subDays(1),
            'total' => 190000.00, // 145000 + 45000
            'cliente_id' => $cliente->id,
            'metodo_pago' => 'Transferencia bancaria',
            'estado' => 'Completada',
            'usuario_id' => $vendedor->id
        ]);

        \App\Models\DetalleVenta::create([
            'venta_id' => $venta3->id,
            'producto_id' => $prodTenisRunning->id,
            'cantidad' => 1,
            'precio_unitario' => 145000.00
        ]);

        \App\Models\DetalleVenta::create([
            'venta_id' => $venta3->id,
            'producto_id' => $prodGafasRetro->id,
            'cantidad' => 1,
            'precio_unitario' => 45000.00
        ]);

        // Venta 4: Cliente compra Botas de Cuero y Cinturón
        $venta4 = \App\Models\Venta::create([
            'fecha' => now(),
            'total' => 270000.00, // 230000 + 40000
            'cliente_id' => $cliente->id,
            'metodo_pago' => 'Efectivo',
            'estado' => 'Completada',
            'usuario_id' => $vendedor->id
        ]);

        \App\Models\DetalleVenta::create([
            'venta_id' => $venta4->id,
            'producto_id' => $prodBotasCuero->id,
            'cantidad' => 1,
            'precio_unitario' => 230000.00
        ]);

        \App\Models\DetalleVenta::create([
            'venta_id' => $venta4->id,
            'producto_id' => $prodCinturonCuero->id,
            'cantidad' => 1,
            'precio_unitario' => 40000.00
        ]);

        // ==========================================
        // REGISTRO DE MOVIMIENTOS EN EL KARDEX (INVENTARIOS)
        // ==========================================
        
        // 1. Registrar entrada inicial para todo el stock
        Inventario::registrarMovimiento($prodJeansSlim->id, 'Entrada', 50, 50);
        Inventario::registrarMovimiento($prodCamisetaAlgodon->id, 'Entrada', 100, 100);
        Inventario::registrarMovimiento($prodVestidoFloral->id, 'Entrada', 30, 30);
        Inventario::registrarMovimiento($prodChaquetaCuero->id, 'Entrada', 20, 20);
        Inventario::registrarMovimiento($prodTenisRunning->id, 'Entrada', 40, 40);
        Inventario::registrarMovimiento($prodBotasCuero->id, 'Entrada', 15, 15);
        Inventario::registrarMovimiento($prodGafasRetro->id, 'Entrada', 60, 60);
        Inventario::registrarMovimiento($prodCinturonCuero->id, 'Entrada', 80, 80);

        // 2. Registrar salidas asociadas a las ventas
        // Venta 1
        Inventario::registrarMovimiento($prodJeansSlim->id, 'Salida', 49, 1);
        $prodJeansSlim->update(['stock' => 49]);
        Inventario::registrarMovimiento($prodCamisetaAlgodon->id, 'Salida', 99, 1);
        $prodCamisetaAlgodon->update(['stock' => 99]);

        // Venta 2
        Inventario::registrarMovimiento($prodChaquetaCuero->id, 'Salida', 19, 1);
        $prodChaquetaCuero->update(['stock' => 19]);

        // Venta 3
        Inventario::registrarMovimiento($prodTenisRunning->id, 'Salida', 39, 1);
        $prodTenisRunning->update(['stock' => 39]);
        Inventario::registrarMovimiento($prodGafasRetro->id, 'Salida', 59, 1);
        $prodGafasRetro->update(['stock' => 59]);

        // Venta 4
        Inventario::registrarMovimiento($prodBotasCuero->id, 'Salida', 14, 1);
        $prodBotasCuero->update(['stock' => 14]);
        Inventario::registrarMovimiento($prodCinturonCuero->id, 'Salida', 79, 1);
        $prodCinturonCuero->update(['stock' => 79]);
    }
}
