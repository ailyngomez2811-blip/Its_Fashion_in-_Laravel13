# Módulo de Productos: Catálogo de Prendas

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/productos.blade.php` (Catálogo y CRUD)
* **Controlador:** `app/Http/Controllers/ProductoController.php` → acciones `index`, `store`, `update`, `show`
* **Modelos:** `app/Models/Producto.php`, `app/Models/Categoria.php`

---

### **Flujos del Módulo**

#### **1. Registrar Prenda (`store`)**
- El administrador ingresa: nombre, descripción, precio_compra, precio_venta, stock, stock_minimo (opcional, por defecto 0), talla, color, categoría y estado inicial (Activo/Inactivo).
- **Validaciones de Integridad y Lógica:**
  - Valida obligatoriedad y tipos de datos.
  - Exige que `precio_compra` y `precio_venta` sean mayores a cero (`gt:0`).
  - **Validación de Rentabilidad:** Valida que el precio de venta sea estrictamente mayor al precio de compra (`(float)$precio_venta > (float)$precio_compra`). Si no se cumple, cancela y advierte.
- Al crearse, registra automáticamente la fila de la entrada inicial de stock en el Kardex y lo deja disponible.

#### **2. Editar Prenda (`update`)**
- Permite actualizar los atributos comerciales de la prenda.
- Aplica las mismas validaciones de precios positivos y rentabilidad de margen de ganancia.
- Si el stock es alterado manualmente durante la edición, el controlador calcula la diferencia e inserta una Entrada o Salida de ajuste en la tabla del Kardex para no perder la trazabilidad física.
