# Módulo de Usuarios: Administración Interna de Accesos

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/admin/usuarios.blade.php` (CRUD)
* **Controlador:** `app/Http/Controllers/UsuarioController.php` → acciones `index`, `store`, `update`, `toggleEstado`
* **Modelos:** `app/Models/User.php`, `app/Models/Rol.php`

---

### **Flujos del Módulo**

#### **1. Crear Usuario Interno (`store`)**
- Permite crear cuentas para el personal de la boutique (Administrador o Empleado). Excluye la creación manual de Clientes (los cuales se registran de forma pública).
- **Validaciones:**
  - Exige que `username` y `email` sean únicos.
  - **Ciberseguridad en Contraseñas:** Valida que cumpla criterios de seguridad mínimos obligatorios: al menos 8 caracteres de longitud, contenga mayúsculas, minúsculas y números (`Password::min(8)->mixedCase()->numbers()`).
- Guarda la cuenta aplicando encriptación criptográfica `Hash::make()`.

#### **2. Editar Usuario (`update`)**
- Permite modificar nombre, apellido, teléfono, email, username y cambiar su rol.
- La contraseña se trata como opcional (`nullable`): solo se vuelve a encriptar y actualizar si el administrador escribe un valor en el campo, permitiendo conservar la contraseña actual si se deja en blanco.

#### **3. Cambiar Estado (`toggleEstado`)**
- Método consumido vía Fetch AJAX desde los switches de la tabla.
- Cambia directamente la columna `estado` del usuario interno. Si pasa a `'Inactivo'`, el sistema le revoca el acceso de manera instantánea, impidiendo que el empleado inicie sesión en la caja o el panel operativo.
