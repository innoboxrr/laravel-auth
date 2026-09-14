# Changelog

Todas las modificaciones notables a este proyecto serán documentadas en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto sigue [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [6.0.1] - 13-09-2026

### Corregido

- El `RouteServiceProvider` del paquete heredaba del de Foundation, que al
  arrancar vuelve a ejecutar el callback de `withRouting()`: la aplicación
  registraba sus propias rutas una vez más. Ahora hereda de `ServiceProvider`,
  registra las rutas del paquete en `boot()` y respeta la caché de rutas. Las
  URIs, los nombres y los middlewares no cambian.

## [6.0.0] - 13-09-2026

Autenticación lista para una SPA en Laravel 13, con tests de cada flujo. Hasta
ahora el paquete se publicaba con tres tests de arranque: los de login y registro
usaban `@test`, que PHPUnit 12 ya no lee.

### Security
 - Suplantar a un usuario pedía sólo `allow-impersonate`, true por omisión:
   cualquier usuario con sesión entraba como cualquier otro, y cualquiera con el
   token entraba sin sesión. Las rutas piden sesión, sólo suplanta quien pasa la
   habilidad `laravel-auth.impersonate` (por omisión un administrador, nunca sobre
   sí mismo ni sobre otro administrador) y el token sólo sirve a quien lo pidió.
 - `forgot-password` ya no revela qué correos tienen cuenta.
 - `create-token` tiene freno de intentos y no inicia sesión.
 - `revoke-token` sólo revoca tokens propios y lo dice.

### Changed
 - `get-auth` devuelve `authenticated`, `is_admin`, `verified` e `impersonating`,
   con el guard de Sanctum. Login y registro devuelven el usuario.
 - Las reglas de contraseña van en `password`; `routes.password` se sigue leyendo.
 - `reset-password` con token inválido y `update-password` con la contraseña
   actual equivocada responden 422 con el error en su campo.
 - `email-verification-notification` responde `{ success, status }`.
 - El correo de restablecer enlaza a `frontend.reset-password`, con el correo
   codificado.
 - Las redirecciones por omisión van a `/admin`, `/auth/login` y `/`.
 - Los mensajes son claves en inglés; el paquete trae `lang/es.json`.

### Fixed
 - Las reglas de contraseña configuradas nunca se aplicaban.
 - Registrarse guardaba el request entero y cifraba la contraseña fuera del
   modelo.
 - La caducidad de un token nunca se guardaba.
 - Un proveedor social sin configurar daba un 500; ahora 404.
 - Los requests importaban `App\Providers\RouteServiceProvider`, que no existe en
   Laravel 13.

### Removed
 - La vista `laravel-auth::impersonate`, que cargaba Tailwind desde un CDN.

## [1.0.4] - 27-02-2023

### Added

### Changed

### Removed

### Fixed
 - Fix: ResetPasswordRequest.php does not comply with psr-4

## [1.0.3] - 27-02-2023

### Added

### Changed

### Removed
 - Se ha removido el folder .phpunit.cache

### Fixed

## [1.0.1] - 27-02-2023

### Added
 - LoginTest
 - RegisterTest

### Changed

### Removed

### Fixed


## [1.0.0] - 27-02-2023

### Added
 - Se han creado todas las rutas de autenticación
 - Se ha configurado el archivo de configuración para el comportamiento de las rutas
 - Se han requerido las dependencias
 	- laravel/sanctum

### Changed
 - No ha ocurrido ningún cambio

### Removed
 - No se ha eliminado nada

### Fixed
 - No se ha corregido nada
