# Social Hub Manager — Guía de instalación y documentación

Este proyecto es un gestor de redes sociales que permite programar y publicar contenido en múltiples plataformas sociales desde una única interfaz.

## Características implementadas

- ✅ Autenticación de usuarios
- ✅ Integración con Twitter (X)
- ✅ Integración con Reddit
- ✅ Programación de publicaciones
- ✅ Cola de publicaciones
- ✅ Sistema de procesamiento en segundo plano
- ✅ Interfaz moderna con Tailwind CSS

## Requisitos previos
- PHP 8.2+
- Composer 2+
- Node.js 18+ y npm 9+
- PostgreSQL 13+ (con un usuario y una base de datos creados)
- Extensiones PHP: `openssl`, `pdo`, `pdo_pgsql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `fileinfo`

## 1) Clonar el repositorio
```bash
# HTTPS
git clone <https://github.com/Salazarmw/Social-Hub-Manager-IWS811.git>
cd Social-Hub-Manager-IWS811
```

## 2) Variables de entorno
Copia el archivo de ejemplo y ajusta valores:
```bash
cp .env.example .env (O crea el archivo .env de forma manual)
```
Edita `.env` y configura al menos:
```env
APP_NAME="Social Hub Manager"
APP_ENV=local
APP_KEY=  # (se genera más adelante)
APP_DEBUG=true
APP_URL=http://localhost

# PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=socialhubmanager
DB_USERNAME=postgres
DB_PASSWORD=postgres

# Sesiones y cache (opcional, recomendado)
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# Mailer (opcional para verificación de email)
MAIL_MAILER=log
```

Si quieres usar un dominio local tipo `social-hub-manager-iws811.test`, agrega una entrada a tu `hosts` (opcional):
- Windows: C:\Windows\System32\drivers\etc\hosts
- Linux/Mac: /etc/hosts
```txt
127.0.0.1   social-hub-manager-iws811.test
```
Y pon `APP_URL=http://social-hub-manager-iws811.test` en `.env`. (O simplemente usa laravel herd)

## 3) Instalar dependencias
```bash
# PHP
composer install

# Node (assets con Vite)
npm install
```

## 4) Generar APP_KEY
```bash
php artisan key:generate
```

## 5) Preparar base de datos
Asegúrate de tener creada la base de datos en PostgreSQL:
```sql
CREATE DATABASE socialhubmanager; (Con el nombre es suficiente, con el migrate se hacen las tables)
```
Luego ejecuta migraciones (y, si aplica, seeders):
```bash
php artisan migrate
# php artisan db:seed   # si tienes seeders
```

## 6) Compilar assets
Modo desarrollo (recarga automática):
```bash
npm run dev
```
Modo producción (build optimizado):
```bash
npm run build
```

## 7) Levantar el servidor
```bash
php artisan serve
# Salida típica: http://127.0.0.1:8000
```
Si usaste dominio personalizado, accede a `http://social-hub-manager-iws811.test` (según tu `APP_URL`). (O simplemente usa laravel herd)

## 8) Estructura relevante
- Rutas web: `routes/web.php`
- Controlador dashboard: `app/Http/Controllers/DashboardController.php`
- Vistas auth personalizadas: `resources/views/auth/`
- Layouts y navegación: `resources/views/layouts/`
- Dashboard: `resources/views/dashboard.blade.php`
- Assets (Vite): `resources/css/app.css`, `resources/js/app.js`

## 9) Comandos útiles
- Limpiar cachés:
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear
```
- Volver a migrar desde cero (¡destruye datos!):
```bash
php artisan migrate:fresh --seed
```

## 10) Solución de problemas
- Error 500 por `APP_KEY` faltante:
  - Ejecuta `php artisan key:generate` y limpia cachés.
- Error de conexión a PostgreSQL:
  - Verifica `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
  - Asegúrate de que el servicio PostgreSQL esté activo.
- Error Vite/JS (módulos no encontrados):
  - Revisa `resources/js/app.js` y vuelve a instalar deps: `rm -rf node_modules && npm install`.
- Cambios en assets no se reflejan:
  - Si usas `npm run dev`, asegúrate de que esté corriendo.
  - Si usas build, corre `npm run build` nuevamente y limpia cachés. (Si usa laravel herd con el run build es suficiente, si ejecuta el npm run dev la aplicación se destruye)



## Estado actual y próximos pasos

### Completado
- ✅ Sistema base de autenticación
- ✅ Integración con Twitter
- ✅ Integración con Reddit
- ✅ Sistema de programación de publicaciones
- ✅ Procesamiento en segundo plano
- ✅ Interfaz de usuario base

### En progreso
- 🔄 Mejoras en la interfaz de usuario
- 🔄 Optimización del sistema de colas
- 🔄 Manejo de errores y reintentos

### Pendiente
- ⏳ Implementación de autenticación de dos factores (2FA)
- ⏳ Conectar los enlaces de la navbar (Publicaciones, Cola, Horarios, Configuración) //Técnicamente ya están, sólo que no sé qué hacer con Horarios, tal vez cambiarlo a algo para ver las publicaciones hechas con datos como fecha, estado, Descripción etc.
- ⏳ Implementar sistema de supervisor o similar para mantener los procesos de cola y scheduler ejecutándose en producción
- ⏳ Mejorar el manejo de errores y reintentos en las publicaciones fallidas

## Servicios necesarios

Para que las publicaciones programadas funcionen correctamente, se requieren dos servicios ejecutándose:

```bash
# Terminal 1: Worker de la cola
php artisan queue:work --daemon

# Terminal 2: Scheduler
php artisan schedule:work
```

