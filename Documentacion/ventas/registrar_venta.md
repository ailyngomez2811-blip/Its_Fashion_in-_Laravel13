# Módulo de Ventas: Registro de Venta

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/ventas.blade.php` (Administrador)
  - `resources/views/empleado/ventas.blade.php` (Empleado)
* **Controlador:** `app/Http/Controllers/VentaController.php` → acción `store`
* **Modelos:** `app/Models/Venta.php`, `app/Models/DetalleVenta.php`, `app/Models/Producto.php`, `app/Models/Caja.php`

---

### **Flujo del Proceso**

1. **Armado del Carrito:** El vendedor añade prendas a la venta indicando cantidades, y asocia opcionalmente a un cliente de la base de datos.
2. **Petición AJAX:** Al guardar la venta, el cliente web envía una petición `POST` con la lista de ítems codificados en formato JSON.
3. **Validación de Caja Abierta:**
   - Si el método de pago seleccionado es `'Efectivo'`, el controlador verifica que exista un registro de caja activo con estado `'Abierta'`. Si no hay caja abierta, cancela la venta retornando una respuesta JSON de error (representada con SweetAlert2 en el frontend).
4. **Validación de Stock en Transacción:**
   - Inicia una transacción de base de datos (`DB::transaction`).
   - Consulta el stock de cada producto usando un bloqueo de lectura y escritura (`lockForUpdate`). Si la cantidad de venta excede el stock físico actual, interrumpe el proceso lanzando una excepción de error.
5. **Cálculo y Guardado de Venta:**
   - Multiplica las cantidades por el precio de venta actual del producto y calcula el valor total de la orden.
   - Crea el registro de venta con fecha, total, `metodo_pago`, `cliente_id` (o null para Mostrador) y `usuario_id` del empleado.
   - Guarda el detalle de los artículos en `detalle_ventas`.
6. **Movimientos de Stock e Ingresos:**
   - Reduce el stock del producto e ingresa la Salida al Kardex llamando a `Inventario::registrarMovimiento()`.
   - Si el cobro es en efectivo, suma el total facturado a la caja activa (`total_ingresos`) y registra un movimiento de tipo `'Ingreso'` en `movimientos_caja`.
