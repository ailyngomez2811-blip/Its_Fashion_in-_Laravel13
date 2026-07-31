# Módulo de Autenticación: Recuperación de Contraseña

---

### **Componentes del Sistema**
* **Vistas:**
  - `resources/views/auth/forgot-password.blade.php` (Solicitud de enlace)
  - `resources/views/auth/reset-password.blade.php` (Formulario de nueva contraseña)
* **Controlador:** Gestionado por el motor nativo de Laravel.

---

### **Flujo del Proceso**

1. **Solicitud de Enlace:**
   - El usuario ingresa su correo en la vista de recuperación.
   - El sistema comprueba que el email exista en la base de datos.
   - Genera un token firmado temporal y lo almacena en la tabla `password_reset_tokens`.
   - Envía un correo con el enlace de recuperación (con el token embebido en la URL) utilizando el servidor SMTP de Gmail configurado (`itsfashion.2026@gmail.com`).
2. **Restablecimiento de Contraseña:**
   - Al hacer clic en el enlace, el usuario accede al formulario de restablecimiento.
   - Introduce el correo y la nueva contraseña.
   - El sistema valida el token de la URL. Si es correcto y no ha expirado, cifra la nueva contraseña con `Hash::make()`, actualiza el usuario, elimina el token usado de la base de datos y redirige al inicio de sesión.