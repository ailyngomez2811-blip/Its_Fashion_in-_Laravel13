# Módulo de Dashboards: Visualización y Acceso por Roles

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/dashboard.blade.php` (Administrador)
  - `resources/views/empleado/dashboard.blade.php` (Empleado)
  - `resources/views/cliente/dashboard.blade.php` (Cliente)
* **Ruta de Acceso:** `/dashboard` (Definida en `routes/web.php`)

---

### **Lógica de Desviación y Carga**

Al ingresar a la ruta `/dashboard`, una función anónima intercepta la sesión. Lee el rol del usuario autenticado e implementa un `match` para bifurcar el acceso:

#### **1. Panel de Administración (`rol_id == 1`)**
- Carga y renderiza `admin.dashboard`.
- **Cálculo de Indicadores Clave (KPIs):** Calcula en tiempo real las estadísticas globales:
  - Total de clientes registrados e identificando cuántos están en estado `'Activo'`.
  - Catálogo total de productos y cantidad de productos en estado `'Activo'`.
  - Sumatoria total de las ventas cobradas hoy (`whereDate` y estado `'Completada'`).
  - Total de ingresos acumulados en el mes en curso.
  - Conteo de solicitudes de devolución que se encuentran en estado `'Pendiente'`.

#### **2. Panel de Empleado (`rol_id == 2`)**
- Carga y renderiza `empleado.dashboard`.
- Diseñado para labores operativas. Calcula y muestra estadísticas del día:
  - Ventas concretadas hoy y monto total cobrado en la jornada para control del cajero.
  - Lista de productos con existencias críticas (debajo del mínimo) y notificaciones de stock.

#### **3. Panel de Cliente (`rol_id == 3`)**
- Carga y renderiza `cliente.dashboard` (o redirecciona directamente a `mis_compras`).
- Oculta todas las métricas corporativas, exponiendo accesos directos únicamente a su historial personal de compras, constancias de devoluciones y edición de perfil.
