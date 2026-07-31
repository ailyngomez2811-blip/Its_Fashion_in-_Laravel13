# Módulo de Caja: Operaciones de Caja

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/caja.blade.php` (Administrador)
  - `resources/views/empleado/caja.blade.php` (Empleado)
* **Controlador:** `app/Http/Controllers/CajaController.php` → acciones `abrir`, `cerrar`, `registrarMovimiento`
* **Modelos:** `app/Models/Caja.php`, `app/Models/MovimientoCaja.php`

---

### **Flujos del Módulo**

#### **1. Apertura de Caja**
- El usuario introduce el saldo inicial del día.
- El controlador valida que el saldo no sea negativo. Consulta que no exista otra fila en la tabla `cajas` con el estado `'Abierta'`.
- Al confirmar que no hay turnos activos duplicados, guarda un registro con la fecha y hora de apertura, saldo inicial, el estado en `'Abierta'` y el ID del usuario responsable.

#### **2. Movimientos Manuales (Ingreso / Egreso)**
- Permite ingresar o retirar dinero de caja manualmente por motivos de ajustes u operaciones diarias (ej. compra de papelería).
- Valida que la caja esté abierta, añade el registro en la tabla `movimientos_caja` y actualiza los campos acumuladores `total_ingresos` o `total_egresos` de la caja abierta activa en la base de datos.

#### **3. Arqueo y Cierre de Caja**
- Al finalizar el turno, el usuario digita el total de efectivo físico contado en caja.
- El sistema calcula el saldo teórico de la siguiente manera:
  `Saldo Teórico = Saldo Inicial + Total Ingresos - Total Egresos`.
- Calcula la diferencia entre lo contado físicamente y el teórico. Si la diferencia es distinta a cero, la validación exige obligatoriamente detallar una justificación.
- Si se proporciona, cierra la caja actualizando el `saldo_final`, la `diferencia`, la `justificacion`, guarda la fecha y hora de cierre y establece el estado en `'Cerrada'`.
