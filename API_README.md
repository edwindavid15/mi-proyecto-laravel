# BarberApp API - StyleRadar

API RESTful completa para la aplicación de barbería StyleRadar, diseñada para ser consumida por aplicaciones móviles Flutter y web.

## 🚀 Características

- **Autenticación robusta** con Laravel Sanctum (tokens API)
- **Control de roles** avanzado (cliente, peluquero, dueño, admin)
- **Sistema de permisos** granular con middlewares personalizados
- **Validación completa** de datos en todos los endpoints
- **Manejo de errores** estructurado
- **Documentación completa** de endpoints
- **Datos de prueba** incluidos con seeders
- **CORS configurado** para desarrollo móvil

## 📋 Requisitos del Sistema

- PHP 8.2+
- Laravel 12.0+
- MySQL/PostgreSQL
- Composer
- Laravel Sanctum

## 🛠️ Instalación y Configuración

### 1. Instalar dependencias
```bash
composer install
```

### 2. Configurar entorno
```bash
cp .env.example .env
php artisan key:generate
```

Configurar base de datos en `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=barber_app
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### 3. Ejecutar migraciones
```bash
php artisan migrate
```

### 4. Poblar base de datos (opcional)
```bash
php artisan db:seed
```

### 5. Iniciar servidor
```bash
php artisan serve
```

## 👥 Sistema de Roles y Permisos

### Roles Disponibles
- **Cliente**: Puede ver servicios, crear/cancelar citas, gestionar perfil
- **Peluquero**: Puede gestionar sus servicios, ver/editar citas asignadas
- **Dueño**: Puede crear peluquerías, gestionar peluqueros, ver estadísticas
- **Admin**: Acceso completo a todas las funcionalidades

### Permisos por Endpoint

| Endpoint | Cliente | Peluquero | Dueño | Admin |
|----------|---------|-----------|-------|-------|
| `POST /api/register` | ✅ | ✅ | ✅ | ✅ |
| `POST /api/login` | ✅ | ✅ | ✅ | ✅ |
| `GET /api/profile` | ✅ | ✅ | ✅ | ✅ |
| `PUT /api/profile` | ✅ | ✅ | ✅ | ✅ |
| `GET /api/servicios` | ✅ | ✅ | ✅ | ✅ |
| `POST /api/servicios` | ❌ | ✅ | ✅ | ✅ |
| `GET /api/citas` | ✅* | ✅* | ❌ | ✅ |
| `POST /api/citas` | ✅ | ❌ | ❌ | ✅ |
| `GET /api/peluquerias` | ✅ | ✅ | ✅ | ✅ |
| `POST /api/peluquerias` | ❌ | ❌ | ✅ | ✅ |
| `GET /api/stats` | ❌ | ❌ | ❌ | ✅ |

*Clientes ven solo sus citas, peluqueros ven citas asignadas

## 📡 Endpoints de la API

### Autenticación

#### Registro de Usuario
```http
POST /api/register
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "cliente",
  "phone": "+1234567890"
}
```

**Respuesta exitosa (201):**
```json
{
  "message": "Usuario registrado correctamente",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "role": "cliente",
    "phone": "+1234567890"
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

#### Inicio de Sesión
```http
POST /api/login
Content-Type: application/json

{
  "email": "juan@example.com",
  "password": "password123"
}
```

#### Cerrar Sesión
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Obtener Perfil
```http
GET /api/profile
Authorization: Bearer {token}
```

#### Actualizar Perfil
```http
PUT /api/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Juan Pérez Actualizado",
  "email": "nuevo@email.com",
  "phone": "+0987654321",
  "current_password": "password123",
  "password": "nueva_password",
  "password_confirmation": "nueva_password"
}
```

### Servicios

#### Listar Servicios
```http
GET /api/servicios
Authorization: Bearer {token}
```

**Filtros disponibles:**
- `?peluqueria_id=1` - Servicios de una peluquería específica
- `?peluquero_id=1` - Servicios de un peluquero específico
- `?active=1` - Solo servicios activos

#### Crear Servicio
```http
POST /api/servicios
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Corte Moderno",
  "descripcion": "Corte contemporáneo con técnicas modernas",
  "precio": 35.00,
  "duracion": 45,
  "peluqueria_id": 1
}
```

#### Ver Servicio
```http
GET /api/servicios/{id}
Authorization: Bearer {token}
```

#### Actualizar Servicio
```http
PUT /api/servicios/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Corte Moderno Premium",
  "precio": 40.00
}
```

#### Eliminar Servicio
```http
DELETE /api/servicios/{id}
Authorization: Bearer {token}
```

### Citas

#### Listar Citas
```http
GET /api/citas
Authorization: Bearer {token}
```

**Filtros disponibles:**
- `?estado=confirmada` - Filtrar por estado
- `?fecha=2024-12-25` - Citas de una fecha específica
- `?proximas=1` - Solo citas próximas

#### Crear Cita
```http
POST /api/citas
Authorization: Bearer {token}
Content-Type: application/json

{
  "peluqueria_id": 1,
  "servicio_id": 1,
  "peluquero_id": 2,
  "fecha": "2024-12-25",
  "hora": "14:30",
  "notas": "Cliente pidió atención especial"
}
```

#### Ver Cita
```http
GET /api/citas/{id}
Authorization: Bearer {token}
```

#### Actualizar Cita
```http
PUT /api/citas/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "estado": "confirmada",
  "notas": "Confirmada por teléfono"
}
```

#### Cancelar Cita
```http
DELETE /api/citas/{id}
Authorization: Bearer {token}
```

### Peluquerías

#### Listar Peluquerías
```http
GET /api/peluquerias
Authorization: Bearer {token}
```

#### Crear Peluquería
```http
POST /api/peluquerias
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Barbería Central",
  "descripcion": "La mejor barbería del centro",
  "direccion": "Calle Principal 123",
  "telefono": "+1234567890",
  "email": "central@barberapp.com",
  "latitud": 40.7128,
  "longitud": -74.0060,
  "horario_apertura": "09:00",
  "horario_cierre": "20:00"
}
```

#### Agregar Peluquero a Peluquería
```http
POST /api/peluquerias/{id}/add-peluquero
Authorization: Bearer {token}
Content-Type: application/json

{
  "peluquero_id": 3
}
```

#### Remover Peluquero de Peluquería
```http
DELETE /api/peluquerias/{id}/remove-peluquero
Authorization: Bearer {token}
Content-Type: application/json

{
  "peluquero_id": 3
}
```

### Estadísticas (Solo Admin)

#### Obtener Estadísticas Generales
```http
GET /api/stats
Authorization: Bearer {token}
```

## 📊 Estados de Citas

- `pendiente` - Cita solicitada, esperando confirmación
- `confirmada` - Cita confirmada por el peluquero
- `en_proceso` - Cita actualmente en ejecución
- `completada` - Cita finalizada exitosamente
- `cancelada` - Cita cancelada
- `no_asistio` - Cliente no asistió a la cita

## 🔒 Seguridad

### Autenticación
- Tokens JWT con Laravel Sanctum
- Expiración automática de sesiones
- Revocación de tokens en logout

### Autorización
- Middlewares personalizados para roles específicos
- Validación de permisos en cada endpoint
- Control de acceso basado en propiedad de recursos

### Validación
- Validación completa de entrada en todos los endpoints
- Sanitización de datos
- Manejo de errores estructurado

## 🚨 Manejo de Errores

### Errores de Validación (422)
```json
{
  "message": "Datos inválidos",
  "errors": {
    "email": ["El campo email ya está en uso."],
    "password": ["El campo password es obligatorio."]
  }
}
```

### Errores de Autorización (403)
```json
{
  "message": "No tienes permisos para acceder a este recurso"
}
```

### Errores de Autenticación (401)
```json
{
  "message": "Usuario no autenticado"
}
```

### Errores del Servidor (500)
```json
{
  "message": "Error interno del servidor",
  "error": "Detalles del error (solo en desarrollo)"
}
```

## 🧪 Testing con Datos de Prueba

Después de ejecutar `php artisan db:seed`, tendrás:

- **1 Administrador**: `admin@barberapp.com` / `password`
- **2 Dueños**: `carlos.dueno@barberapp.com`, `maria.dueno@barberapp.com`
- **3 Peluqueros**: `juan.peluquero@barberapp.com`, `ana.peluquero@barberapp.com`, `pedro.peluquero@barberapp.com`
- **3 Clientes**: `luis.cliente@barberapp.com`, `carmen.cliente@barberapp.com`, `roberto.cliente@barberapp.com`
- **3 Peluquerías** con servicios y citas de prueba

## 📱 Consumo desde Flutter

### Configuración del Cliente HTTP

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String baseUrl = 'http://10.0.2.2:8000/api'; // Android emulator
  String? _token;

  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    if (_token != null) 'Authorization': 'Bearer $_token',
  };

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode({'email': email, 'password': password}),
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      _token = data['token'];
      return data;
    } else {
      throw Exception('Error en login: ${response.body}');
    }
  }

  Future<List<dynamic>> getServicios() async {
    final response = await http.get(
      Uri.parse('$baseUrl/servicios'),
      headers: _headers,
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return data['servicios'];
    } else {
      throw Exception('Error al obtener servicios');
    }
  }
}
```

## 🔄 Próximos Pasos

1. **Implementar notificaciones push** para recordatorios de citas
2. **Agregar sistema de calificaciones** y reseñas
3. **Implementar pagos integrados** (Stripe, PayPal)
4. **Agregar geolocalización** para encontrar peluquerías cercanas
5. **Sistema de promociones** y descuentos
6. **API de imágenes** para subir fotos de trabajos
7. **Integración con calendario** de Google/Outlook

## 📞 Soporte

Para soporte técnico o preguntas sobre la API, contacta al equipo de desarrollo.

---

**Desarrollado con ❤️ para la comunidad de barbería**