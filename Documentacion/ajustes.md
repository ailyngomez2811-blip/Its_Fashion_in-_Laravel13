# Módulo de Configuración: Ajustes del Sistema y Variables de Entorno

---

### **Componentes del Sistema**
* **Archivo de Configuración Global:** `.env`
* **Controlador de Perfil:** `app/Http/Controllers/ProfileController.php`
* **Vistas de Perfil:** `resources/views/profile/edit.blade.php`

---

### **1. Configuración de Entorno (.env)**
El sistema lee las variables clave para adaptar el comportamiento según el entorno de despliegue (Desarrollo local o Producción):

- **`APP_ENV`**: Establecido en `local` para desarrollo y cambia a `production` al subirlo a internet.
- **`APP_DEBUG`**: Activa los mensajes de error explícitos en desarrollo (`true`), pero debe apagarse (`false`) en producción para evitar fugas de información de seguridad.
- **`APP_URL`**: URL base del sistema (ej. `http://localhost` para local y `https://itsfashion.com` en producción). Se usa para generar las firmas y rutas en los correos enviados.
- **`MAIL_*` (Servicio de Envío de Email)**:
  - Integra SMTP seguro de Google (Gmail).
  - Cuenta emisora: `itsfashion.2026@gmail.com`.
  - Autenticación mediante contraseña de aplicación encriptada de 16 caracteres.
  - Cifrado seguro `TLS` por puerto `587`.

---

### **2. Ajustes de Perfil de Usuario (`ProfileController`)**
Permite que el Administrador, Empleado o Cliente actualicen la información asociada a su propia cuenta:
- **Actualizar Datos Básicos:** Permite cambiar nombre, apellido, username, correo electrónico y teléfono. Aplica la regla `unique` ignorando el ID del usuario en curso para evitar conflictos.
- **Actualizar Contraseña:** Exige ingresar la contraseña actual (para validar pertenencia mediante `Hash::check()`) y definir la nueva clave que se encripta con `Hash::make()`.
- **Cierre de Sesión Seguro:** Al desautenticarse, invalida la sesión web actual y regenera el token de seguridad CSRF para prevenir secuestro de sesiones.
