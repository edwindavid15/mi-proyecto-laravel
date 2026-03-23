f# 📋 Resumen de Cambios - BarberApp API
## 23 de Marzo de 2026

---

## 🎯 ¿Qué es BarberApp?

**BarberApp** es una aplicación de gestión para peluquerías que permite administrar:
- **Usuarios** (Administradores, Peluqueros, Clientes)
- **Peluquerías** (Barbershops)
- **Servicios** (Cortes, tinturas, etc.)
- **Citas** (Reservas de clientes)

---

## 🏗️ Arquitectura Actual: API REST

Hemos transformado el proyecto a una **Arquitectura de API REST + Base de Datos**. Esto significa:

✅ **Backend**: Laravel (API)  
✅ **Frontend**: Se construirá por separado (Flutter, React, Vue, etc.)  
✅ **Comunicación**: JSON sobre HTTP (sin vistas HTML)

### ¿Por qué esta arquitectura?
- **Escalabilidad**: Múltiples clientes (web, mobile, app de escritorio)
- **Separación de responsabilidades**: Backend y Frontend independientes
- **Reutilización**: El mismo API sirve para cualquier cliente
- **Fácil de testear**: Cada endpoint es independiente

---

## 📁 Estructura de Carpetas Explicada

```
app/
├── Http/
│   ├── Controllers/Api/          ← Controladores REST (NUEVOS)
│   │   ├── AuthController.php    ← Autenticación (login, registro)
│   │   ├── CitaController.php    ← Gestión de citas
│   │   ├── PeluqueriaController.php ← Gestión de peluquerías
│   │   └── ServicioController.php ← Gestión de servicios
│   │
│   └── Middleware/               ← Autenticación y autorización (NUEVOS)
│       ├── CheckRole.php         ← Verifica un rol específico
│       └── CheckAnyRole.php      ← Verifica si tiene cualquiera de varios roles
│
├── Models/                        ← Modelos de datos (MODIFICADOS)
│   ├── User.php                  ← Usuario (admin, peluquero, cliente)
│   ├── Cita.php                  ← Cita/Reserva
│   ├── Peluqueria.php            ← Barbershop/Peluquería
│   └── Servicio.php              ← Servicio ofrecido
│
config/
├── cors.php                       ← Configuración CORS (NUEVO)
├── sanctum.php                    ← Configuración de tokens API (NUEVO)
│
database/
├── migrations/
│   └── 2026_03_05_120000_create_personal_access_tokens_table.php ← (NUEVO)
│
└── seeders/                       ← Datos de prueba (NUEVOS)
    ├── CitaSeeder.php
    ├── PeluqueriaSeeder.php
    ├── ServicioSeeder.php
    └── UserSeeder.php

routes/
├── api.php                        ← Rutas del API (NUEVO)
├── auth.php                       ← Rutas de autenticación Laravel existentes
└── web.php                        ← Rutas web (ahora vacías)
```

---

## 🔑 Cambios Implementados

### 1️⃣ **Controladores API (app/Http/Controllers/Api/)**

#### `AuthController.php` - Autenticación
**¿Para qué sirve?**
- Registro de nuevos usuarios
- Login (genera token de acceso)
- Logout (revoca el token)

**Endpoints:**
- `POST /api/auth/register` → Crear cuenta nueva
- `POST /api/auth/login` → Obtener token de acceso
- `POST /api/auth/logout` → Cerrar sesión

**¿Por qué lo creamos?**
Sin esto, los clientes (flutter, web) no tenían forma de autenticarse en la API.

---

#### `CitaController.php` - Gestión de Citas
**¿Para qué sirve?**
- Crear, leer, actualizar y eliminar citas
- Filtrar citas por peluquería, peluquero, cliente, fecha

**Endpoints:**
- `GET /api/citas` → Listar citas
- `POST /api/citas` → Crear cita
- `GET /api/citas/{id}` → Ver cita específica
- `PUT /api/citas/{id}` → Actualizar cita
- `DELETE /api/citas/{id}` → Cancelar cita

**Ejemplo de respuesta:**
```json
{
  "id": 1,
  "cliente_id": 2,
  "peluquero_id": 3,
  "servicio_id": 1,
  "peluqueria_id": 1,
  "fecha_hora": "2026-03-25 14:00:00",
  "estado": "confirmada"
}
```

---

#### `PeluqueriaController.php` - Gestión de Peluquerías
**¿Para qué sirve?**
- Listar peluquerías disponibles
- Ver detalles de cada peluquería
- Administrar peluquerías (admin)

**Endpoints:**
- `GET /api/peluquerias` → Listar todas
- `GET /api/peluquerias/{id}` → Ver detalles
- `POST /api/peluquerias` → Crear (admin)
- `PUT /api/peluquerias/{id}` → Actualizar (admin)

---

#### `ServicioController.php` - Gestión de Servicios
**¿Para qué sirve?**
- Listar servicios disponibles (cortes, tinturas, etc.)
- Ver precios y características
- Administrar catálogo de servicios

**Endpoints:**
- `GET /api/servicios` → Listar servicios
- `GET /api/servicios/{id}` → Ver detalles
- `POST /api/servicios` → Crear (admin)
- `PUT /api/servicios/{id}` → Actualizar (admin)

---

### 2️⃣ **Middlewares de Autorización (app/Http/Middleware/)**

#### `CheckRole.php`
**¿Para qué sirve?**
Verifica que el usuario autenticado tenga exactamente el rol requerido.

**Ejemplo de uso en rutas:**
```php
Route::put('/peluquerias/{id}', UpdatePeluqueriaAction::class)
    ->middleware('auth:sanctum', 'role:admin');
```

**¿Por qué lo necesitamos?**
Para que solo los administradores puedan modificar datos sensibles.

---

#### `CheckAnyRole.php`
**¿Para qué sirve?**
Verifica que el usuario tenga AL MENOS uno de los roles especificados.

**Ejemplo:**
```php
Route::post('/citas', CitaController::class)
    ->middleware('auth:sanctum', 'anyRole:admin,peluquero');
```

**¿Por qué lo necesitamos?**
Algunos endpoints pueden ser usados por múltiples tipos de usuarios (admin Y peluqueros pueden crear citas).

---

### 3️⃣ **Configuración CORS (config/cors.php)** - ⭐ IMPORTANTE

**¿Qué es CORS?**
Es un mecanismo de seguridad del navegador. Sin esto, tu app Flutter/React en otro dominio NO puede acceder a la API.

**¿Para qué sirve?**
- Permite que clientes desde otros dominios accedan a la API
- Configura qué dominios tienen permiso
- Especifica qué headers y métodos HTTP están permitidos

**Configuración actual:**
```php
'allowed_origins' => ['*'],  // Todos los dominios (desarrollo)
                             // En producción: especificar dominio exacto
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
```

---

### 4️⃣ **Sanctum (config/sanctum.php)** - Tokens API

**¿Qué es Sanctum?**
Sistema de autenticación sin sesiones para APIs. Genera tokens en lugar de cookies.

**¿Para qué sirve?**
- Generar tokens de acceso al hacer login
- Validar tokens en cada request
- Revocar tokens al hacer logout

**Flujo:**
```
Cliente hace POST /api/auth/login
    ↓
Servidor valida credenciales
    ↓
Servidor devuelve token (ej: "abc123xyz...")
    ↓
Cliente guarda token en local storage
    ↓
Cliente envía en cada request: Header "Authorization: Bearer abc123xyz..."
    ↓
Servidor valida y procesa la solicitud
```

---

### 5️⃣ **Tabla de Tokens Personales**

**Migración:** `2026_03_05_120000_create_personal_access_tokens_table.php`

**¿Para qué sirve?**
Almacena los tokens generados así como sus expiraciones y permisos.

**¿Por qué se creó?**
Sin esta tabla, Sanctum no puede guardar ni validar tokens.

---

### 6️⃣ **Seeders (Datos de Prueba)**

#### `UserSeeder.php`
Crea usuarios de prueba:
- 1 Admin
- 3 Peluqueros  
- 5 Clientes

```bash
php artisan db:seed --class=UserSeeder
```

---

#### `PeluqueriaSeeder.php`
Crea 3 peluquerías de ejemplo con:
- Nombre, dirección, teléfono
- Asigna peluqueros a cada una

---

#### `ServicioSeeder.php`
Crea servicios disponibles:
- Corte de cabello ($15)
- Barba ($10)
- Tintura ($25)
- Paquete completo ($40)

---

#### `CitaSeeder.php`
Crea citas de ejemplo para probar el sistema:
- Citas pasadas, presentes y futuras
- Diferentes estados (confirmada, cancelada, pendiente)

---

## ❌ ¿Por Qué Eliminamos Estos Archivos?

### 🗑️ Controladores Web Eliminados
```
app/Http/Controllers/
  ❌ CitaController.php       (viejo, para web)
  ❌ PeluqueriaController.php (viejo, para web)
  ❌ ProfileController.php    (viejo, para web)
  ❌ ServicioController.php   (viejo, para web)
```

**¿Por qué?**
- Eran controladores para renderizar vistas HTML
- El nuevo sistema es API REST (devuelve JSON, no HTML)
- Los nuevos están en `Controllers/Api/` y son superiores

---

### 🗑️ Vistas Blade Eliminadas
```
resources/views/
  ❌ auth/login.blade.php, register.blade.php, etc.
  ❌ citas/create.blade.php, index.blade.php, etc.
  ❌ servicios/create.blade.php, etc.
  ❌ layouts/, components/, dashboard.blade.php, etc.
```

**¿Por qué?**
- **Cambio de arquitectura**: De monolítica (todo en Laravel) a separada (API + Cliente)
- **Los clientes** (Flutter, React) renderizarán su propia UI
- **Laravel solo gestiona datos** (API), no presentación
- Reduce duplicación de código
- Cada cliente puede tener su propio diseño/experiencia

**Ejemplo:**
```
ANTES (Laravel Monolítico):
  Cliente solicita página → Laravel genera HTML → Browser renderiza

AHORA (API REST):
  Cliente solicita datos → API devuelve JSON → Cliente renderiza UI
  (El cliente podría ser Flutter, React, Vue, etc.)
```

---

### 🗑️ Rutas Web Eliminadas
```
routes/web.php
  ❌ Todas las rutas de vistas
  ✅ Ahora vacío o solo para actualizaciones futuras
```

---

## 📊 Relaciones entre Modelos

```
User (ID, email, password, role)
  ├─ role = 'admin' → Administra todo
  ├─ role = 'peluquero' → Atiende citas
  └─ role = 'cliente' → Reserva citas

Peluqueria (ID, nombre, dirección, teléfono)
  ├─ Tiene muchos usuarios (peluqueros)
  └─ Tiene muchos servicios

Servicio (ID, nombre, precio, descripción, peluquero_id)
  └─ Pertenece a una Peluquería

Cita (ID, cliente_id, peluquero_id, servicio_id, peluqueria_id, fecha_hora)
  ├─ Cliente es un User
  ├─ Peluquero es un User  
  └─ Servicio es un Servicio
```

---

## 🔐 Sistema de Roles

### Tres Roles Implementados:

| Rol | Permisos |
|-----|----------|
| **admin** | Crear/editar/eliminar peluquerías, servicios, usuarios. Ver todas las citas. |
| **peluquero** | Ver citas asignadas. Marcar como completadas. No puede crear servicios. |
| **cliente** | Ver servicios disponibles. Crear, ver y cancelar sus propias citas. |

### Ejemplo de Restricción:
```php
// Solo admin puede crear peluquería
Route::post('/peluquerias', 'PeluqueriaController@store')
    ->middleware('auth:sanctum', 'role:admin');

// Admin o peluquero pueden ver citas
Route::get('/citas', 'CitaController@index')
    ->middleware('auth:sanctum', 'anyRole:admin,peluquero');

// Solo cliente puede crear cita propia
Route::post('/citas', 'CitaController@store')
    ->middleware('auth:sanctum', 'role:cliente');
```

---

## 📚 Archivos de Documentación

### `API_README.md` (NUEVO)
Guía completa del API con:
- Endpoints disponibles
- Parámetros requeridos
- Ejemplos de respuestas
- Códigos de error

### `BarberApp_API_Postman_Collection.json` (NUEVO)
Colección Postman para probar todos los endpoints:
1. Descargar [Postman](https://www.postman.com/)
2. Importar el archivo JSON
3. Probar todos los endpoints sin escribir código

---

## 🚀 Próximas Tareas para tu Equipo

### 1. **Crear Cliente Flutter**
```bash
flutter new barber_app_mobile
cd barber_app_mobile
# Instalar dependencias para conectar API
flutter pub add http dio
```

### 2. **Crear Cliente Web (React/Vue)**
```bash
npm create vite@latest barber_app_web -- --template react
# o
npm create vite@latest barber_app_web -- --template vue
```

### 3. **Testear API con Postman**
- Importar `BarberApp_API_Postman_Collection.json`
- Ejecutar requests de ejemplo
- Verificar respuestas

### 4. **Desplegar a Producción**
- Cambiar `CORS allowed_origins` a dominio real
- Configurar base de datos real
- Implementar HTTPS
- Proteger claves en `.env`

### 5. **Agregar Funcionalidades**
- Pagos (Stripe, PayPal)
- Notificaciones (SMS, Email)
- Reseñas/Ratings
- Disponibilidad de horarios

---

## 📖 Documentación Útil

- **Laravel API**: https://laravel.com/docs
- **Sanctum**: https://laravel.com/docs/sanctum
- **CORS**: https://developer.mozilla.org/es/docs/Web/HTTP/CORS
- **REST APIs**: https://restfulapi.net/

---

## 💡 Resumiendo: Qué Cambió y Por Qué

| Antes | Ahora | ¿Por Qué? |
|-------|-------|----------|
| Monolítica (todo en Laravel) | API + Cliente separado | Escalabilidad |
| Vistas HTML generadas | JSON devuelto | Múltiples clientes |
| Controladores web | Controladores API | Mejor estructura |
| Sin autenticación API | Tokens Sanctum | Seguridad |
| Sin CORS | CORS configurado | Acceso desde otros dominios |
| Datos sin roles | Sistema de roles completo | Seguridad y control |

---

## 📍 Nuevas funcionalidades para MVP “Uber de peluqueros”

### 1. Esquema de datos extendido
- `users`: ahora incluye `latitud`, `longitud`, `is_online`.
- `peluquerias`: incluye `is_active` (solo las activas se listan para clientes).
- `servicios`: ya incluye `peluquero_id`, `duracion`, precio.

### 2. Localización y búsqueda cercana
- Endpoint: `GET /api/peluquerias/nearby?latitud=...&longitud=...&radio=...`
  - Devuelve peluquerías en un radio (km) basado en Haversine.
- Endpoint: `GET /api/peluqueros/nearby?latitud=...&longitud=...&radio=...`
  - Devuelve peluqueros con `role=peluquero`, `is_online=true`, y ubicación válida.

### 3. Control de estado de peluquero
- En `profile` y `updateProfile` ya se pueden enviar:
  - `latitud`, `longitud`, `is_online`
- Permite ciclado rápido de disponibilidad de cada peluquero.

### 4. Separación de modelos para independencia
- `Peluqueria`: datos físicos, coordenadas, duración etc.
- `Peluquero`: usuario con rol (independiente), puede tener servicios propios y ubicación propia.
- `Servicio`: asociado a `peluquero_id` (independiente) y opcionalmente `peluqueria` mediante convención.

---

## 📝 Notas para el Equipo

✅ **El código está en GitHub**: https://github.com/edwindavid15/mi-proyecto-laravel.git

✅ **Todos pueden clonar y trabajar** en diferentes ramas

✅ **Usar commits descriptivos**: `git commit -m "feat: agregar endpoint de pagos"`

✅ **Seguir la estructura**: Nuevos endpoints en `Controllers/Api/`, nuevas rutas en `routes/api.php`

✅ **Testear localmente** antes de hacer push

---

**Preguntas del equipo sobre la arquitectura?**  
📧 Escriban en la documentación o abran issues en GitHub para discutir cambios.

---

*Documento generado: 23 de Marzo de 2026*
