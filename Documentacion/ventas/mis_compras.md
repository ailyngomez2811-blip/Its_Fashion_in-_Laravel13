# Módulo de Ventas: Consultar Compras (Cliente)

---

### **Componentes del Sistema**
* **Vista:** `resources/views/cliente/mis_compras.blade.php`
* **Controlador:** `app/Http/Controllers/VentaController.php` → acción `index`
* **Modelos:** `app/Models/Venta.php`, `app/Models/DetalleVenta.php`

---

### **Flujo del Proceso**

1. **Petición del Historial:** El cliente accede a su área de "Mis Compras".
2. **Filtrado por Sesión:**
   - El controlador evalúa el rol del usuario autenticado. Al identificar que es un Cliente (`rol_id === 3`), restringe la consulta SQL de ventas filtrando únicamente los registros donde `cliente_id` coincida con el ID de su sesión (`Auth::id()`), aislando por completo la información de otras cuentas.
3. **Desglose del Pedido (AJAX):**
   - Al hacer clic en un pedido, realiza una petición fetch al endpoint `/ventas/{id}`. El backend valida nuevamente la propiedad del pedido.
   - Si es correcto, devuelve un JSON con el desglose de productos, cantidades, precios unitarios, método de pago y estado de la orden para renderizar el detalle interactivo.
4. **Acceso a Devoluciones:**
   - Si la compra posee estado `'Completada'`, el sistema le habilita al cliente un selector numérico local sobre cada producto del detalle para que pueda iniciar de forma autónoma una solicitud de devolución.
