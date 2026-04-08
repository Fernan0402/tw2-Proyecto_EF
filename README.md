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

   **Opción B — SQL manual:** ejecutar el script sobre la misma base donde ya existe la tabla `users` (y el resto de tablas que uses).

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
FROM php:8.4-apache
 
ENV APACHE_DOCUMENT_ROOT=/var/www/html/webroot
 
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      libzip-dev libonig-dev libpng-dev libicu-dev zlib1g-dev libxml2-dev ca-certificates git unzip curl; \
    docker-php-ext-install pdo pdo_mysql mysqli mbstring zip intl opcache xml; \
    a2enmod rewrite headers; \
    rm -rf /var/lib/apt/lists/*
 
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!<Directory /var/www/html>!<Directory ${APACHE_DOCUMENT_ROOT}>!g" /etc/apache2/apache2.conf
 
WORKDIR /var/www/html
 
COPY app-ef/ /var/www/html
 
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R u+w /var/www/html/tmp /var/www/html/logs || true
 
EXPOSE 80
 
CMD ["apache2-foreground"]
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


## Uso

- **Registro:** enlace «Registrar» en la barra superior (sin sesión).
- **Inicio de sesión:** `/` (ruta por defecto).
- **Tareas:** cada usuario solo ve y gestiona sus propias tareas; en el listado hay filtros por estado, rango de fecha límite y texto.
- **Perfil:** idioma de interfaz entre los anteriores y datos personales; el código se guarda en `perfiles.idioma` (p. ej. `es_ES`, `zh_CN`) y se sincroniza con `users.language`.

## Estructura de base de datos relevante

- `users` — usuarios (incluye `language` para compatibilidad).
- `tareas` — `user_id`, `titulo`, `descripcion_es`, `descripcion_en`, `estado`, `fecha_limite`.

