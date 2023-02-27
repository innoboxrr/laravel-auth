# Sistema de autenticación alternativo para Laravel

Este es un sistema de autenticación alternativo para Laravel que provee rutas seguras para la autenticación y gestión de tokens. Las siguientes son las rutas disponibles:

## Rutas de autenticación

### Inicio de sesión
POST /login
Esta ruta es utilizada para autenticar a un usuario.

### Registro
POST /register
Esta ruta es utilizada para registrar a un nuevo usuario.

### Cierre de sesión
POST /logout
Esta ruta es utilizada para cerrar la sesión de un usuario.

### Olvido de contraseña
POST /forgot-password
Esta ruta es utilizada para iniciar el proceso de recuperación de contraseña.

### Restablecimiento de contraseña
POST /reset-password
Esta ruta es utilizada para restablecer la contraseña de un usuario.

### Notificación de verificación de correo electrónico
POST /email-verification-notification
Esta ruta es utilizada para enviar notificaciones de verificación de correo electrónico.

### Verificación de correo electrónico
GET /email-verification
Esta ruta es utilizada para verificar el correo electrónico de un usuario.

## Rutas de tokens
### Crear token
POST /create-token
Esta ruta es utilizada para crear un nuevo token para un usuario.

### Tokens
POST /tokens
Esta ruta es utilizada para obtener todos los tokens asociados a un usuario.

### Revocar token
POST /revoke-token
Esta ruta es utilizada para revocar el token de un usuario.

### Vaciar tokens
POST /flush-tokens
Esta ruta es utilizada para revocar todos los tokens de un usuario.

# RouteServiceProvider

El Service Provider `RouteServiceProvider` es responsable de mapear las rutas de autenticación. Esto incluye la carga de las rutas definidas en los archivos de rutas de la aplicación y la configuración de los parámetros de ruta.

## Funciones principales

### map()

El método `map()` es el método principal del Service Provider `RouteServiceProvider`. Este método es llamado automáticamente por Laravel cuando el Service Provider es registrado en la aplicación.

Dentro de este método, se deben registrar todas las rutas de la aplicación.

### mapAuthRoutes()

El método `mapAuthRoutes()` es un ejemplo de cómo se pueden registrar las rutas de autenticación de la aplicación.

En este ejemplo, el método `mapAuthRoutes()` registra las rutas de autenticación definidas en el archivo `routes/auth.php` utilizando el middleware `web`.

Además, este método utiliza la configuración definida en el archivo `config/laravel-auth.php` para establecer el prefijo y el namespace de las rutas.

También en este archivo de configuración se encuentra la opción `route.active`, que define si las rutas deben ser cargadas dentro de la aplicación.

## Uso

Para versiones anteriores de Laravel 5.5 y para utilizar el Service Provider `RouteServiceProvider`, debes registrarlo en tu aplicación. Esto se hace en el archivo `config/app.php`.

Asegúrate de que la clase `RouteServiceProvider` esté registrada en la sección `providers` del archivo `config/app.php`:

`
'providers' => [
// ...
Innoboxrr\LaravelAuth\Providers\RouteServiceProvider::class,
],
`

Una vez que hayas registrado el Service Provider, las rutas definidas en los archivos de rutas de la aplicación serán cargadas automáticamente.