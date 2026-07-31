# Módulo de Inventario: Control de Existencias y Kardex

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/inventario.blade.php` (Inventario y Kardex)
* **Controlador:** `app/Http/Controllers/InventarioController.php` → acción `index`
* **Modelos:** `app/Models/Inventario.php` (Kardex), `app/Models/Producto.php`, `app/Models/Categoria.php`

---

### **1. Kardex de Movimientos**
La tabla `inventarios` actúa como el Kardex contable del negocio. Documenta cada alteración del stock de prendas de la boutique:

* **Método Auxiliar:** `Inventario::registrarMovimiento($producto_id, $tipo, $stock_resultante, $cantidad)`
* **Tipos de Movimiento:**
  - **Entrada:** Ocurre al registrar compras de mercancía a proveedores, o al aprobar devoluciones de prendas aceptadas por el administrador.
  - **Salida:** Ocurre al concretar ventas a clientes en caja.

---

### **2. Visualización e Indicadores de Existencias**
La vista principal desglosa indicadores calculados dinámicamente en base al nivel de stock y stock mínimo configurado:

- **Total Productos:** Conteo de todas las prendas registradas.
- **Con Stock:** Productos cuyas existencias son mayores a cero.
- **Stock Crítico:** Prendas que cuentan con existencias pero cuya cantidad actual es menor o igual al valor de `stock_minimo` establecido. El sistema resalta el valor con un color amarillo/ámbar de advertencia.
- **Sin Stock:** Conteo de productos agotados (`stock == 0`).

---

### **3. Buscador y Filtros Interactivos**
El frontend realiza búsquedas en tiempo real en la tabla por:
- Barra de búsqueda de texto (nombre, ID, talla o color).
- Filtrado rápido por categorías de prendas.
- Filtrado rápido por estado de existencias (Con stock, Crítico o Sin stock).
