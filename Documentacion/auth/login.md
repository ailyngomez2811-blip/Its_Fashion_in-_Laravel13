# Módulo de Autenticación: Inicio de Sesión (Login)

---

### **Componentes del Sistema**
* **Vista:** `resources/views/auth/login.blade.php`  
* **Controlador:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php` → acción `store`
* **FormRequest de Validación:** `app/Http/Requests/Auth/LoginRequest.php` → acción `authenticate`
* **Modelo:** `app/Models/User.php`

---

### **Flujo del Proceso**

1. **Envío de Formulario:** El usuario llena los campos y el formulario realiza un envío vía `POST` al endpoint `/login`.
2. **Validación y Filtros (FormRequest):** 
   - El archivo `LoginRequest.php` intercepta la solicitud. Valida que los campos `username` y `password` no estén vacíos.
   - Determina si la entrada corresponde a una dirección de correo o a un nombre de usuario de acceso con un filtro de correo electrónico (`FILTER_VALIDATE_EMAIL`).
3. **Verificación de Estado de Cuenta:**
   - Realiza una consulta directa a la base de datos para buscar al usuario por su email o username.
   - Si se localiza la cuenta pero el campo `estado` es `'Inactivo'`, el sistema detiene inmediatamente el proceso lanzando una excepción de validación (`ValidationException`) con el mensaje personalizado: *"Esta cuenta se encuentra inactiva. Por favor, ponte en contacto con el administrador para reactivar tu acceso."*
4. **Intento de Autenticación:**
   - Si la cuenta está activa, ejecuta `Auth::attempt()` pasando las credenciales.
   - Si la contraseña cifrada no coincide (usando la verificación criptográfica interna), incrementa los intentos en el Limitador de Tasa (`RateLimiter`) y rechaza la sesión con el mensaje genérico de credenciales inválidas.
5. **Redirección por Rol:**
   - Al autenticarse correctamente, se limpia el limitador y se regenera la sesión para prevenir ataques de fijación.
   - El controlador de sesión redirige a la ruta `/dashboard`.
   - El endpoint `/dashboard` evalúa el rol del usuario mediante un `match` por su `rol_id`:
     - **case 1 (Administrador):** Redirige a la vista `admin.dashboard`.
     - **case 2 (Empleado):** Redirige a la vista `empleado.dashboard`.
     - **case 3 (Cliente):** Redirige a la vista `cliente.dashboard`.
