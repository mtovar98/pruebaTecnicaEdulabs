# Storage Control (Prueba Técnica)

Aplicación web sencilla para gestión de archivos con reglas de seguridad y cuotas de almacenamiento.  
Stack: **Laravel 12 (Blade)** + **MySQL** + **Vanilla JS (ES6+)** + **Tailwind**.

## Objetivo
Permitir a los usuarios subir archivos, listarlos, descargarlos y eliminarlos, aplicando reglas:
- **Cuotas**: prioridad **Usuario > Grupo > Global**.
- **Bloqueo por extensión prohibida** (ej. `exe, bat, js, php, sh`).
- **Inspección de .zip**: si contiene un archivo interno con extensión prohibida, se rechaza.

---

## Requisitos
- PHP 8.2+ con extensiones: `pdo_mysql`, `zip`
- MySQL 8+ (o MariaDB 10.4+)
- Composer, Node 18+/npm

## Instalación

```bash
git clone <TU_REPO>
cd storage-control

# Backend
cp .env.example .env
# Edita .env (ver sección Configuración)
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link

# Frontend (assets)
npm install
npm run dev  # o npm run build en producción

# Levantar
php artisan serve
# http://127.0.0.1:8000


Configuración (.env)

Ejemplo mínimo:
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=storage_control
DB_USERNAME=laravel
DB_PASSWORD=laravel123

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public


Seed de datos
php artisan db:seed


