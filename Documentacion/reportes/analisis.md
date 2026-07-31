# Módulo de Reportes: Estadísticas y Exportaciones

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/reportes.blade.php` (Gráficos y KPIs)
  - `resources/views/admin/reportes_pdf.blade.php` (Plantilla PDF)
  - `resources/views/admin/reportes_excel.blade.php` (Plantilla XLS)
* **Controlador:** `app/Http/Controllers/ReporteController.php` → acciones `index`, `exportarPdf`, `exportarExcel`
* **Modelos:** `app/Models/Venta.php`, `app/Models/Compra.php`, `app/Models/Devolucion.php`, `app/Models/Producto.php`

---

### **Flujos del Módulo**

#### **1. Panel de Control y Gráficos (`index`)**
- Permite filtrar las estadísticas por períodos rápidos (Esta semana, Este mes, Últimos 3 meses, Este año) o por un rango de fechas personalizado.
- **Gráfica Principal (Chart.js):** Pinta de forma dinámica el comportamiento diario o mensual de las ventas completadas.
- **KPIs:** Calcula facturación acumulada, ticket promedio del período, porcentaje de cobro por método (Efectivo vs Transferencia) e inversión en abastecimiento.
- **Listados:** Carga top 5 de prendas más vendidas, últimas devoluciones y el inventario consolidado.

#### **2. Exportación a PDF (`exportarPdf`)**
- El administrador selecciona mediante checkboxes las secciones que desea anexar al reporte (Ventas, Inventario, Productos más vendidos, Devoluciones).
- Procesa las consultas en el rango de fechas seleccionado y renderiza la vista HTML `admin.reportes_pdf`.
- Convierte el HTML a PDF usando la librería `dompdf` y fuerza la apertura en línea en el navegador.

#### **3. Exportación a Excel (`exportarExcel`)**
- Genera una vista HTML tabular simple (`admin.reportes_excel`) y añade en la cabecera de la respuesta HTTP el byte de orden de bytes UTF-8 (`\xEF\xBB\xBF`) para dar soporte a caracteres especiales en Microsoft Excel. Descarga el archivo con extensión `.xls`.
