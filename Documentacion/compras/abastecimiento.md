# Módulo de Compras: Abastecimiento y Entradas

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/compras.blade.php` (Administrador)
  - `resources/views/empleado/compras.blade.php` (Empleado)
* **Controlador:** `app/Http/Controllers/CompraController.php` → acciones `index`, `store`, `show`
* **Modelos:** `app/Models/Compra.php`, `app/Models/DetalleCompra.php`, `app/Models/Producto.php`, `app/Models/Proveedor.php`

---

### **Flujos del Módulo**

#### **1. Crear Compra (`store`)**
- El empleado o administrador añade productos al listado de compras especificando el proveedor, las unidades recibidas y el costo de adquisición unitario.
- Envía una petición `POST` al backend con la estructura en JSON.
- **Transacción Contable y Física:** Abre una transacción (`DB::transaction`):
  1. Suma el costo de adquisición de todas las prendas y guarda la cabecera en `compras` con la fecha, total, `proveedor_id` y `usuario_id` del responsable.
  2. Por cada prenda ingresada: guarda la fila en `detalle_compras` con su subtotal.
  3. Ejecuta un incremento sobre la columna `stock` en la tabla `productos` mediante un bloqueo exclusivo (`lockForUpdate`).
  4. Llama a `Inventario::registrarMovimiento()` para asentar la **Entrada** de stock en el Kardex.
- Responde una confirmación en JSON que el frontend muestra mediante SweetAlert2.

#### **2. Visualizar Detalle de Compra (`show`)**
- Retorna la lista detallada de los ítems de una compra (producto, talla, color, unidades ingresadas y precio unitario) para desglosarlo interactivamente en los modales de consulta de la tienda.
