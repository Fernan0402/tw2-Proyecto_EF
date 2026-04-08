# App de tareas (CakePHP 5)

Aplicación web de gestión de tareas por usuario con registro, sesión, perfil con idioma (español, inglés), CRUD de tareas, filtros y descripciones bilingües por tarea.

## Requisitos

- PHP 8.2 o superior
- Composer
- MariaDB o MySQL 5.7+ (compatible con el driver `Mysql` de CakePHP)
- Apache con `mod_rewrite` **o** el servidor integrado de PHP/Cake (`bin/cake server`)

## Instalación

1. Clonar el repositorio e ir al directorio del proyecto.

2. Instalar dependencias PHP:

   ```bash
   composer install
   ```

3. Configurar la base de datos: copiar `config/app_local.example.php` a `config/app_local.php` y editar el array `Datasources` con host, usuario, contraseña y nombre de la base de datos.

4. Crear la base de datos vacía en MariaDB/MySQL.

5. Aplicar el esquema:

   **Opción A — migraciones (recomendado):**

   ```bash
   bin/cake migrations migrate
   ```

   **Opción B — SQL manual:** ejecutar el script [config/schema/entregable_tareas.sql](config/schema/entregable_tareas.sql) sobre la misma base donde ya existe la tabla `users` (y el resto de tablas que uses).

## Cómo ejecutar

**Servidor de desarrollo CakePHP:**

```bash
bin/cake server -p 8765
```

Abrir `http://localhost:8765` (la raíz muestra el inicio de sesión).

**Apache:** configurar el `DocumentRoot` al directorio `webroot/` del proyecto.

final 

# 🐳 Despliegue de Aplicación CakePHP con Podman

## 📌 Descripción

Este proyecto consiste en la contenerización de una aplicación web desarrollada en **CakePHP**, utilizando **Podman** como motor de contenedores.

Se creó una imagen personalizada basada en PHP con Apache, configurando las extensiones necesarias para el correcto funcionamiento del sistema, y se orquestó mediante `podman-compose`.

---

## ⚙️ Tecnologías utilizadas

* PHP 8.2 + Apache
* CakePHP
* Podman
* Podman Compose
* Linux

---

## 📁 Estructura del proyecto

```
devops/
├── Dockerfile
├── compose.yml
└── app_ef/   # Aplicación CakePHP
```

---

## 🚀 Pasos de implementación

### 1️⃣ Crear carpeta de trabajo

```bash
mkdir ~/devops/
cd ~/devops/
```

📌 **¿Qué hace?**
Crea una carpeta llamada `devops` y entra en ella para trabajar.

---

### 2️⃣ Colocar la aplicación

Copiar o clonar el proyecto dentro de la carpeta:

```
app_ef/
```

📌 **¿Qué hace?**
Contiene todo el código fuente de la aplicación CakePHP.

---

### 3️⃣ Crear el Dockerfile

```dockerfile
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-install intl pdo pdo_mysql mysqli

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar aplicación
COPY app_ef/ /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Puerto
EXPOSE 80
```

📌 **¿Qué hace?**
Define cómo se construye la imagen:

* Usa PHP con Apache
* Instala extensiones necesarias (`intl`, `pdo_mysql`, etc.)
* Copia la aplicación al contenedor
* Configura permisos

---

### 4️⃣ Crear compose.yml

```yaml
services:
  php-app:
    image: ef-app
    container_name: ef-app
    ports:
      - "8080:80"
    restart: unless-stopped
```

📌 **¿Qué hace?**
Define cómo se ejecuta el contenedor:

* Usa la imagen creada (`ef-app`)
* Expone el puerto 8080
* Mantiene el contenedor activo

---

### 5️⃣ Configuración de red (opcional)

```bash
sudo mousepad /etc/containers/containers.conf
```

Agregar:

```ini
[engine]
network_cmd = "host"
```

📌 **¿Qué hace?**
Permite que Podman use la red del host, evitando problemas de conexión.

---

### 6️⃣ Construir la imagen

```bash
podman build -t ef-app .
```

📌 **¿Qué hace?**
Crea una imagen llamada `ef-app` a partir del Dockerfile.

---

### 7️⃣ Verificar imágenes

```bash
podman images
```

📌 **¿Qué hace?**
Muestra las imágenes disponibles en el sistema.

---

### 8️⃣ Ejecutar contenedor

```bash
podman-compose up
```

📌 **¿Qué hace?**
Levanta el contenedor definido en `compose.yml`.

---

### 9️⃣ Acceder a la aplicación

```
http://localhost:8080
```

📌 **¿Qué hace?**
Permite acceder a la aplicación desde el navegador.

---

## 🔍 Comandos útiles

### Ver puertos en uso

```bash
sudo ss -tuln
```

📌 Muestra los puertos ocupados en el sistema.

---

### Ver contenedores activos

```bash
podman ps
```

📌 Lista los contenedores en ejecución.

---

### Ver logs del contenedor

```bash
podman logs ef-app
```

📌 Muestra errores o información del contenedor.

---

## 🛠 Problemas solucionados

* ❌ Error en COPY (ruta incorrecta)
  ✔ Se corrigió el nombre de carpeta (`app_ef`)

* ❌ Falta de extensión `intl`
  ✔ Se instaló con `docker-php-ext-install`

* ❌ Error de conexión MySQL
  ✔ Se agregó `pdo_mysql` y `mysqli`

* ❌ Error de imagen inexistente
  ✔ Se ejecutó `podman build`

---

## ✅ Resultado final

* Aplicación funcionando en contenedor
* Acceso vía navegador
* Entorno reproducible
* Configuración portable

---

## 👨‍💻 Autor

Proyecto desarrollado como parte de la materia **Tecnología Web II**.


## Uso

- **Registro:** enlace «Registrar» en la barra superior (sin sesión).
- **Inicio de sesión:** `/` (ruta por defecto).
- **Tareas:** cada usuario solo ve y gestiona sus propias tareas; en el listado hay filtros por estado, rango de fecha límite y texto.
- **Perfil:** idioma de interfaz entre los anteriores y datos personales; el código se guarda en `perfiles.idioma` (p. ej. `es_ES`, `zh_CN`) y se sincroniza con `users.language`.
- **Países / Usuarios:** módulos adicionales de ejemplo (requieren sesión).

## Estructura de base de datos relevante

- `users` — usuarios (incluye `language` para compatibilidad).
- `perfiles` — una fila por usuario (`idioma`, `biografia`).
- `tareas` — `user_id`, `titulo`, `descripcion_es`, `descripcion_en`, `estado`, `fecha_limite`.

## Documentación del entregable

- [docs/INFORME_IMRD.md](docs/INFORME_IMRD.md) — informe en formato IMRD.
- [docs/BITACORA_IA.md](docs/BITACORA_IA.md) — bitácora de uso de IA.
- [docs/EVIDENCIAS.md](docs/EVIDENCIAS.md) — qué capturas o video conviene entregar.

## Licencia

MIT (igual que el esqueleto oficial de CakePHP).
