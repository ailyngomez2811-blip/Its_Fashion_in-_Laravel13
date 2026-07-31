# Módulo de Autenticación: Registro de Clientes (Register)

---

### **Componentes del Sistema**
* **Vista:** `resources/views/auth/register.blade.php`  
* **Controlador:** `app/Http/Controllers/Auth/RegisteredUserController.php` → acción `store`
* **Modelos:** `app/Models/User.php`, `app/Models/Rol.php`

---

### **Flujo del Proceso**

1. **Captura de Datos:** El cliente ingresa sus datos (nombre, apellido, username, teléfono, email, contraseña y confirmación de contraseña).
2. **Validación:**
   - Valida que todos los campos sean obligatorios.
   - Aplica la regla `unique:users` para los campos `username` y `email`, impidiendo que existan cuentas duplicadas en el sistema.
   - Valida que la contraseña cumpla los mínimos exigidos y coincida con el campo de confirmación (`confirmed`).
3. **Asignación del Rol:**
   - El sistema busca automáticamente en la tabla `roles` el ID del registro que tenga la descripción `'Cliente'`.
4. **Cifrado y Almacenamiento:**
   - Se crea el nuevo registro del usuario con su `rol_id` de cliente y aplicando la función `Hash::make()` para almacenar la contraseña con un cifrado criptográfico seguro.
5. **Finalización:**
   - Envía el evento de registro de Laravel (`Registered`).
   - Redirecciona de vuelta al inicio de sesión (`/login`) con un mensaje Flash de éxito: *"¡Registro exitoso! Ya puedes iniciar sesión."* (El usuario no es autenticado de manera automática para garantizar la seguridad de su acceso inicial).
