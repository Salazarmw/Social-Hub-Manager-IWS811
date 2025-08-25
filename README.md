# Social Hub Manager — Guía de instalación y documentación

Este proyecto es un gestor de redes sociales que permite programar y publicar contenido en múltiples plataformas sociales desde una única interfaz.

## Características implementadas

- ✅ Autenticación de usuarios con protección de rutas
- ✅ Integración con Twitter (X) - Publicaciones automáticas
- ✅ Integración con Reddit - Publicaciones automáticas  
- ✅ Programación de publicaciones
- ✅ Cola de publicaciones con procesamiento en segundo plano
- ✅ Sistema de procesamiento automático con cron jobs
- ✅ **Calendario de horarios interactivo** con vista mensual, semanal y diaria
- ✅ **Horarios recurrentes** (ej: Lunes y Miércoles a las 9 AM)
- ✅ **Horarios específicos** (fecha y hora exacta)
- ✅ **Autenticación de dos factores (2FA)** para acciones sensibles
- ✅ Interfaz moderna con Tailwind CSS y componentes interactivos
- ✅ Sistema de notificaciones y mensajes de éxito/error

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

### 🌐 URLs principales de la aplicación:
- **Dashboard:** `/dashboard` - Panel principal con resumen
- **Calendario:** `/calendar` - Gestión de horarios de publicación
- **Cola:** `/queue` - Monitoreo de publicaciones programadas  
- **Configuración:** `/settings` - Conexión de cuentas sociales y 2FA
- **Perfil:** `/profile` - Gestión de perfil de usuario

## 8) Estructura relevante
- **Rutas principales:**
  - `routes/web.php` - Todas las rutas web y API
  - `routes/auth.php` - Rutas de autenticación
- **Controladores principales:**
  - `app/Http/Controllers/DashboardController.php` - Dashboard principal
  - `app/Http/Controllers/CalendarController.php` - Sistema de calendario
  - `app/Http/Controllers/PostController.php` - Gestión de publicaciones
  - `app/Http/Controllers/QueueController.php` - Gestión de cola de trabajos
  - `app/Http/Controllers/OAuthController.php` - Integración con redes sociales
  - `app/Http/Controllers/TwoFactorController.php` - Sistema 2FA
- **Modelos principales:**
  - `app/Models/PublishingSchedule.php` - Horarios de publicación
  - `app/Models/ScheduledPost.php` - Publicaciones programadas
  - `app/Models/SocialAccount.php` - Cuentas sociales conectadas
- **Vistas principales:**
  - `resources/views/calendar/` - Interfaz del calendario
  - `resources/views/dashboard.blade.php` - Dashboard principal
  - `resources/views/auth/` - Vistas de autenticación personalizadas
  - `resources/views/layouts/` - Layouts y navegación
- **Jobs y comandos:**
  - `app/Jobs/PublishScheduledPost.php` - Publicación automática
  - `app/Jobs/ProcessScheduledPublications.php` - Procesamiento de horarios
  - `app/Console/Commands/ProcessScheduledPublications.php` - Comando de consola
- **Assets:** 
  - `resources/css/app.css` - Estilos principales
  - `resources/js/app.js` - JavaScript principal

## 9) Comandos útiles para desarrollo

### Limpieza de cachés
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear
```

### Base de datos
```bash
# Volver a migrar desde cero (¡destruye datos!)
php artisan migrate:fresh --seed
```

### Monitoreo y debugging
```bash
# Ver logs en tiempo real (Windows PowerShell)
Get-Content "storage\logs\laravel.log" -Wait -Tail 10

# Ver logs en tiempo real (Linux/Mac)
tail -f storage/logs/laravel.log

# Procesar horarios manualmente
php artisan schedule:process-publications

# Verificar cola de trabajos
php artisan queue:work --verbose
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

### ✅ Completado
- ✅ **Sistema de calendario avanzado** con FullCalendar.js
  - Vista mensual, semanal y diaria con navegación fluida
  - Scroll completo de 24 horas en vistas de tiempo
  - Título dinámico que muestra mes/año/semana/día actual
- ✅ **Horarios de publicación flexibles**
  - Horarios específicos: fecha y hora exacta
  - Horarios recurrentes: selección de días de la semana con rango de fechas opcional
  - Validación avanzada para evitar conflictos de datos
- ✅ **Autenticación de dos factores (2FA)**
  - Protección para acciones sensibles (perfil, desconectar cuentas)
  - Códigos TOTP compatibles con Google Authenticator
  - Middleware de verificación automática
- ✅ **Base de datos optimizada**
  - Índices optimizados para consultas de calendario
- ✅ **Interfaz de usuario mejorada**
  - Formularios con validación en tiempo real
  - Mensajes de éxito y error informativos
  - Componentes responsivos con Tailwind CSS

### ⏳ Pendiente
- ⏳ Dashboard con estadísticas de publicaciones
- ⏳ Historial detallado de publicaciones realizadas
- ⏳ Sistema de notificaciones push
- ⏳ Implementar sistema de supervisor para producción
- ⏳ Exportación de reportes de actividad

## Servicios necesarios

Para que las publicaciones programadas funcionen correctamente, se requieren dos servicios ejecutándose:

```bash
# Terminal 1: Worker de la cola
php artisan queue:work --daemon

# Terminal 2: Scheduler
php artisan schedule:work
```

