# Módulo de Categorías: Gestión de Categorías

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/categorias.blade.php` (Catálogo y listados)
* **Controlador:** `app/Http/Controllers/CategoriaController.php` → acciones `index`, `store`, `update`, `destroy`
* **Modelos:** `app/Models/Categoria.php`, `app/Models/Producto.php`

---

### **Flujos del Módulo**

#### **1. Crear Nueva Categoría (`store`)**
- El administrador registra el `nombre` (obligatorio) y la `descripcion` (opcional).
- El sistema valida la unicidad del nombre (`unique:categorias,nombre`) para evitar duplicados.
- Guarda la categoría en la tabla `categorias` y la deja disponible inmediatamente.

#### **2. Editar Categoría (`update`)**
- Permite al administrador corregir erratas o cambiar el nombre y descripción.
- Valida la unicidad del nombre ignorando el registro que se está editando (`Rule::unique('categorias', 'nombre')->ignore($categoria->id)`).
- Al actualizarse, todos los productos relacionados apuntan automáticamente a la versión corregida a través de la clave foránea `categoria_id`.

#### **3. Control de Eliminación (`destroy`)**
- El administrador solicita eliminar una categoría.
- **Validación de integridad referencial:** Antes de borrar, realiza una consulta rápida usando `DB::table('productos')->where('categoria_id', $categoria->id)->exists()`.
- Si existen productos en inventario vinculados a esa categoría, bloquea la acción para no dejar productos huérfanos de clasificación y lanza un toast de error: *"No se puede eliminar: tiene productos asociados."*
- Si no tiene productos vinculados, elimina permanentemente la categoría de la base de datos.
