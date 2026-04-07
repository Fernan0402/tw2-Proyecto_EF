# App EF - Sistema de Gestión

Aplicación CakePHP 5.x para la gestión de tareas, pagos y usuarios con sistema de roles y permisos.

## Tabla de Contenidos

1. [Descripción](#descripción)
2. [Requisitos del Sistema](#requisitos-del-sistema)
3. [Estructura del Proyecto](#estructura-del-proyecto)
4. [Instalación](#instalación)
5. [Configuración](#configuración)
6. [Base de Datos](#base-de-datos)
7. [Ejecución del Proyecto](#ejecución-del-proyecto)


---

## Descripción

**App EF** es una aplicación web desarrollada en CakePHP 5.x que permite:

- Gestión de tareas personales (CRUD completo)
- Gestión de pagos con múltiples métodos
- Sistema de autenticación con idioma persistente
- Control de acceso basado en roles (RBAC)

---

## Requisitos del Sistema
PHP 8.2 o superior
Composer
MariaDB o MySQL 5.7+ (compatible con el driver Mysql de CakePHP)
Apache con mod_rewrite o el servidor integrado de PHP/Cake (bin/cake server)

---

## Estructura del Proyecto

```
app-ef/
├── config/                 # Configuración de CakePHP
│   ├── app.php            # Configuración principal
│   ├── app_local.php      # Configuración local (DB)
│   ├── bootstrap.php      # Bootstrap de la aplicación
│   └── routes.php         # Rutas de la aplicación
├── db_ef.sql              # Script SQL de la base de datos
├── src/
│   ├── Controller/        # Controladores
│   │   ├── AppController.php
│   │   ├── UsersController.php
│   │   ├── TasksController.php
│   │   └── PagosController.php
│   ├── Model/
│   │   ├── Table/        # Tablas de la ORM
│   │   │   ├── UsersTable.php
│   │   │   ├── TasksTable.php
│   │   │   └── PagosTable.php
│   │   └── Entity/       # Entidades
│   │       ├── User.php
│   │       ├── Task.php
│   │       └── Pago.php
│   └── Application.php   # Clase principal de la app
├── templates/             # Vistas de CakePHP
│   ├── layout/           # Plantillas maestro
│   ├── Users/            # Vistas de usuarios
│   ├── Tasks/            # Vistas de tareas
│   └── Pagos/            # Vistas de pagos
├── vendor/               # Dependencias de Composer
├── webroot/             # Archivos públicos
└── tmp/                 # Archivos temporales
```

---

## Instalación

### 1. Clonar o copiar el proyecto

```bash
cd /ruta/del/proyecto
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar variables de entorno

Copiar el archivo de ejemplo:

```bash
cp config/.env.example config/.env
```

Editar `config/.env` con los valores correctos:

```
```

### 4. Limpiar caché

```bash
bin/cake cache clear_all
```

---

## Configuración

### Archivo app_local.php

Editar `config/app_local.php` con los datos de tu base de datos:

```php
'Datasources' => [
    'default' => [
        'host' => '172.25.0.220',
        'port' => 3306,
        'username' => 'tu_usuario',
        'password' => 'tu_contraseña',
        'database' => 'db_ef',
    ],
],
```

---

## Base de Datos

### Crear la base de datos

```sql
CREATE DATABASE db_ef CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Importar estructura y datos

```bash
# Desde la línea de comandos
mysql -u tu_usuario -p db_ef < db_ef.sql

# O desde phpMyAdmin
# Importar el archivo db_ef.sql
```

### Estructura de Tablas

#### Tabla `users`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID primario |
| nombre | VARCHAR(250) | Nombre del usuario |
| apellido | VARCHAR(250) | Apellido del usuario |
| correo | VARCHAR(250) | Correo electrónico (único) |
| password | VARCHAR(255) | Contraseña hasheada |
| telefono | VARCHAR(50) | Teléfono (opcional) |
| language | VARCHAR(10) | Idioma (es/en) |
| rol | ENUM | Rol del usuario (admin/empleado/usuario) |
| created | DATETIME | Fecha de creación |
| modified | DATETIME | Fecha de modificación |

#### Tabla `tasks`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID primario |
| user_id | INT | ID del usuario propietario |
| title | VARCHAR(255) | Título de la tarea |
| description | TEXT | Descripción |
| status | VARCHAR(32) | Estado (pending/in_progress/completed) |
| due_date | DATE | Fecha límite |
| created | DATETIME | Fecha de creación |
| modified | DATETIME | Fecha de modificación |

#### Tabla `pagos`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID primario |
| user_id | INT | ID del usuario propietario |
| metodo | ENUM | Método de pago |
| monto | DECIMAL(10,2) | Monto del pago |
| estado | ENUM | Estado del pago |
| descripcion | TEXT | Descripción |
| fecha_pago | DATETIME | Fecha de pago |
| fecha_creacion | DATETIME | Fecha de creación |
| fecha_actualizacion | DATETIME | Fecha de modificación |

---

## Ejecución del Proyecto

### Servidor de desarrollo

```bash
# Puerto por defecto 8765
bin/cake server

# Puerto específico
bin/cake server 8080
```

Acceder a: http://localhost:8765

### Con PHP integrado

```bash
php -S localhost:8765 -t webroot
```

---

## Sistema de Roles y Permisos

### Roles disponibles

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **admin** | Administrador | Acceso completo a todos los CRUD (Users, Tasks, Pagos). Gestión de usuarios. |
| **empleado** | Empleado | CRUD completo de Tasks y Pagos. Solo su perfil. |
| **usuario** | Usuario estándar | CRUD de sus propias Tasks y Pagos. Solo su perfil. |

### Restricciones importantes

- **El rol 'admin' NO puede ser asignado desde la interfaz**.
- Solo puede crearse directamente desde phpMyAdmin o mediante SQL.
- Los usuarios se registran con rol 'usuario' por defecto.

### Matriz de Permisos

| Recurso | Admin | Empleado | Usuario |
|---------|-------|----------|---------|
| **Users** | | | |
| - Ver lista | ✓ | ✗ | ✗ |
| - Ver perfil (cualquiera) | ✓ | ✗ | ✗ |
| - Crear usuario | ✓ | ✓ | ✗ |
| - Editar usuario | ✓ | Propio | Propio |
| - Eliminar usuario | ✓ | ✗ | ✗ |
| **Tasks** | | | |
| - Ver todas | ✓ | ✓ | ✗ |
| - Crear | ✓ | ✓ | ✓ |
| - Editar todas | ✓ | Propia | Propia |
| - Eliminar todas | ✓ | Propia | Propia |
| **Pagos** | | | |
| - Ver todos | ✓ | ✓ | ✗ |
| - Crear | ✓ | ✓ | ✓ |
| - Editar todos | ✓ | Propio | Propio |
| - Eliminar todos | ✓ | Propio | Propio |

---

## Credenciales de Ejemplo

### Usuario Administrador (crear manualmente desde phpMyAdmin)

```sql
-- Contraseña: admin123
INSERT INTO users (nombre, apellido, correo, password, rol) 
VALUES ('Admin', 'Sistema', 'admin@app.com', '$2y$12$BYcOh1z0Z1KM1ZGxIP4iRuN2YWHjnP/bY3UyjCqwL.1VjPVB64jNy', 'admin');
```

**Credenciales de acceso**:
- Correo: admin@app.com
- Contraseña: admin123

### Usuarios de ejemplo (incluidos en db_ef.sql)

| Correo | Contraseña | Rol |
|--------|------------|-----|
| fernandomendez@gmail.com | (hash existente) | usuario |
| cristia@gmail.com | (hash existente) | usuario |
| fer.nan@gmail.com | (hash existente) | usuario |

---

## Rutas Disponibles

| Ruta | Descripción |
|------|-------------|
| / | Redirecciona a login o tareas |
| /users/login | Iniciar sesión |
| /users/register | Registrarse |
| /users/profile | Mi perfil |
| /tasks | Lista de tareas |
| /tasks/add | Nueva tarea |
| /pagos | Lista de pagos |
| /pagos/add | Nuevo pago |

---

## Troubleshooting

### Error de conexión a base de datos

Verificar credenciales en `config/app_local.php` y que el servidor MySQL esté corriendo.

### Error de caché

Limpiar la caché:
```bash
bin/cake cache clear_all
```

### Error de permisos en tmp

```bash
chmod -R 777 tmp/
```

---

## Licencia

MIT License - CakePHP Framework
