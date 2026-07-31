# Módulo de Devoluciones: Reembolsos y Resoluciones

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/devoluciones.blade.php` (Resoluciones)
  - `resources/views/cliente/mis_devoluciones.blade.php` (Solicitudes)
* **Controlador:** `app/Http/Controllers/DevolucionController.php` → acciones `index`, `store`, `aprobar`, `rechazar`
* **Modelos:** `app/Models/Devolucion.php`, `app/Models/DetalleDevolucion.php`, `app/Models/Venta.php`, `app/Models/Caja.php`

---

### **Flujos del Módulo**

#### **1. Solicitar Devolución (Cliente - `store`)**
- El cliente selecciona productos de un pedido completado indicando cantidades y el motivo.
- El controlador valida que las cantidades solicitadas no superen a las facturadas en la venta original.
- **Registro en Espera:** Crea la devolución con estado `'Pendiente'`, calcula el monto preliminar de reintegro e inserta los detalles correspondientes.
- **Notificación:** Emite una notificación interna en la campana de avisos para todos los administradores y empleados anunciando la nueva solicitud.

#### **2. Aprobar Devolución (Administrador - `aprobar`)**
- Flujo exclusivo del administrador (`rol_id === 1`).
- Abre una transacción segura (`DB::transaction`):
  1. Cambia el estado de la solicitud a `'Aceptada'`.
  2. Reinyecta las prendas al stock de `productos` incrementando sus valores.
  3. Registra una **Entrada** por devolución en el Kardex (`Inventario::registrarMovimiento()`).
  4. **Movimiento Contable:** Si hay una caja abierta activa en el turno, el sistema inserta una fila de tipo `'Egreso'` en `movimientos_caja` por el importe de la devolución y actualiza el campo `total_egresos` de la caja abierta para que cuadre el arqueo final.

#### **3. Rechazar Devolución (Administrador - `rechazar`)**
- Flujo exclusivo del administrador. Cambia el estado de la solicitud a `'Rechazada'`, agregando observaciones explicativas del rechazo. No altera stock de almacén ni flujo de efectivo de caja.
