# Sistema de Polla Mundialista

Aplicacion Laravel para gestionar torneos de futbol, usuarios verificados por celular, inscripciones manuales con coordinacion de pago por WhatsApp, pronosticos, puntuacion automatica, ranking y panel administrativo Filament.

## Stack

- Laravel 12.x, compatible con PHP 8.2 en este entorno.
- MySQL recomendado para produccion.
- Breeze para autenticacion web.
- Filament para panel administrativo.
- Spatie Laravel Permission para roles.
- TailwindCSS, Alpine.js y Livewire.
- Queues, Scheduler, Notifications, Policies-ready, seeders, factories y tests.

> Laravel 13.x es la rama estable mas reciente al momento de este trabajo, pero requiere una version de PHP superior a la disponible localmente. Por eso este proyecto usa Laravel 12.x con PHP 8.2.

## Requisitos

- PHP 8.2 o superior.
- Composer.
- Node.js y npm.
- MySQL/MariaDB.
- Extensiones PHP habituales de Laravel: mbstring, openssl, pdo, tokenizer, xml, ctype, json, fileinfo.

## Instalacion

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
```

Configura MySQL en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=polla_mundialista
DB_USERNAME=root
DB_PASSWORD=
```

## Comandos de ejecucion

```bash
php artisan serve
php artisan queue:work
php artisan schedule:work
```

El scheduler ejecuta cada 10 minutos:

```bash
php artisan matches:check-pending-results
```

## Credenciales demo

Panel admin:

- URL: `/admin`
- Celular o email: `admin@example.com` o `999999999`
- Password: `password`

Usuarios demo:

- `demo1@example.com`, `987654321`, password `password`
- `demo2@example.com`, `987654322`, password `password`
- `demo3@example.com`, `987654323`, password `password`

## OTP SMS/WhatsApp

Modo local recomendado:

```env
OTP_PROVIDER=log
OTP_CHANNEL_DEFAULT=whatsapp
OTP_EXPIRES_MINUTES=10
OTP_RESEND_SECONDS=60
OTP_MAX_ATTEMPTS=5
```

Con `OTP_PROVIDER=log`, el codigo OTP queda en `storage/logs/laravel.log`.

El proyecto incluye estas implementaciones:

- `LogOtpSender`
- `SmsOtpSender`
- `WhatsAppOtpSender`

`SmsOtpSender` y `WhatsAppOtpSender` son placeholders seguros para integrar Twilio, Vonage, Meta WhatsApp Cloud API, 360dialog u otro proveedor. No hay credenciales quemadas en codigo.

## WhatsApp para pagos

El sistema no procesa pagos. Solo crea la inscripcion y muestra un link:

```text
https://wa.me/{numero}?text={mensaje}
```

Puedes configurar un numero global:

```env
PAYMENT_WHATSAPP_NUMBER=51999999999
```

O un numero por torneo desde Filament.

## Roles

Roles iniciales:

- `super_admin`
- `tournament_admin`
- `user`

El panel Filament solo permite acceso a usuarios activos con rol `super_admin` o `tournament_admin`.

## Tests

```bash
php artisan test
```

La suite cubre registro, login, OTP, inscripcion, bloqueo de pronosticos, guardado de pronostico, cierre, scoring, ranking y registro de resultados.

## Nota legal

Este sistema no procesa pagos ni apuestas directamente. Si se usa para actividades con dinero real, el propietario debe revisar y cumplir la normativa legal aplicable en su pais antes de operar.
