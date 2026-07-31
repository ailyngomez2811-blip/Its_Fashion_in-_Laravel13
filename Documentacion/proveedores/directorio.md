# Módulo de Proveedores: Directorio Comercial

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/proveedores.blade.php` (Directorio y CRUD)
* **Controlador:** `app/Http/Controllers/ProveedorController.php` → acciones `index`, `store`, `update`, `show`, `destroy`
* **Modelos:** `app/Models/Proveedor.php`

---

### **Flujos del Módulo**

#### **1. Registrar Proveedor (`store`)**
- Registra nombre de la empresa, persona de contacto de ventas, teléfono, email (opcional), dirección de despacho (opcional) y documento de identificación fiscal (opcional).
- Aplica la validación `'unique:proveedores,documento'` para prevenir registros duplicados de una misma entidad en el sistema comercial.

#### **2. Editar Proveedor (`update`)**
- Permite actualizar los datos de contacto del proveedor.
- Valida la regla única de documento ignorando el ID actual del registro en edición.

#### **3. Restricciones de Borrado (`destroy`)**
- Para asegurar la integridad contable y auditorías de abastecimiento, el sistema realiza una comprobación previa: `DB::table('compras')->where('proveedor_id', $proveedor->id)->exists()`.
- Si el proveedor posee compras/facturas registradas a su nombre, el sistema bloquea su eliminación y avisa con un mensaje de advertencia. Si no tiene movimientos vinculados, borra el registro de forma permanente.
