# Módulo de Clientes: Gestión y Auditoría de Clientes

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/clientes.blade.php` (Listado y KPIs)
* **Controlador:** `app/Http/Controllers/ClienteController.php` → acciones `index`, `toggleEstado`, `compras`, `devoluciones`
* **Modelos:** `app/Models/User.php`, `app/Models/Venta.php`, `app/Models/Devolucion.php`

---

### **Flujos del Módulo**

#### **1. Listar y Buscar Clientes (`index`)**
- Consulta los usuarios que tengan el rol de cliente (`rol_id === 3`), contando mediante `withCount` el total de sus ventas y devoluciones asociadas.
- En la interfaz, habilita búsquedas en tiempo real en la tabla por nombre, apellido, documento, teléfono o correo.

#### **2. Activar / Desactivar Cliente (`toggleEstado`)**
- El administrador puede suspender el acceso de un cliente. 
- Al llamarse, alterna el valor de la columna `estado` entre `'Activo'` e `'Inactivo'`. Si pasa a inactivo, la cuenta no podrá iniciar sesión en el sistema, pero se conserva intacto su historial de compras y devoluciones.

#### **3. Consultar Historial de Compras (`compras`)**
- Retorna una respuesta JSON con todas las ventas asociadas al ID del cliente.
- Desglosa por cada venta: la fecha, el total, el método de pago, el estado y concatena en una cadena los productos adquiridos junto con sus cantidades para mostrar un resumen descriptivo en el modal.

#### **4. Consultar Historial de Devoluciones (`devoluciones`)**
- Busca y lista a través de la relación de ventas todas las solicitudes de devoluciones registradas por el cliente, mostrando su estado (Pendiente, Aceptada, Rechazada), el motivo y el monto de dinero reembolsado.
