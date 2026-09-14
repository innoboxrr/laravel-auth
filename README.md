# Laravel Auth

Autenticación completa para una aplicación Laravel 13 con una SPA delante: login,
registro, recuperación y cambio de contraseña, verificación de correo, tokens de
Sanctum, login social y suplantación de usuarios. Todo responde JSON cuando la
petición lo pide y redirige cuando no.

Es el backend de acceso de las aplicaciones que monta
[innoboxrr/laravel-setup](https://github.com/innoboxrr/laravel-setup), en Vue o en
React.

## Apoya Nuestro Trabajo 🙌

Desarrollamos estos paquetes de software de manera gratuita con la intención de contribuir a la comunidad de Laravel y facilitar la vida de los desarrolladores. Nos apasiona compartir lo que hemos aprendido y ver cómo nuestros paquetes ayudan a las personas en sus proyectos.

Sin embargo, también requerimos de apoyo para seguir creando y manteniendo estos recursos. Si estás en la posición de poder hacerlo, te invitamos a inscribirte a uno de nuestros cursos de pago. No solo estarías apoyando nuestro trabajo, sino que también podrías adquirir nuevas habilidades y conocimientos.

En particular, te recomendamos nuestro curso [Desarrollo de paquetes en Laravel para mejorar tu productividad](https://laravelers.com/course/275). Este curso está diseñado para enseñarte a desarrollar tus propios paquetes de Laravel y PHP. Al inscribirte, no solo estarás apoyando nuestro trabajo, sino que también estarás invirtiendo en tu propio crecimiento y desarrollo como programador.

Gracias por considerar esta opción y por tu apoyo continuo a nuestra labor. ¡Apreciamos enormemente a nuestra comunidad!

## Requisitos

| | Versión |
|---|---|
| PHP | ^8.3 |
| Laravel | ^13.0 |
| laravel/sanctum | ^4.0 |
| laravel/socialite | ^5.16 |

## Instalación

```bash
composer require innoboxrr/laravel-auth
php artisan install:api
```

Las rutas se registran solas bajo `/auth`, en el grupo `web`, con nombres
`auth.*`.

Lo que el paquete espera de la aplicación:

- **Sanctum en modo SPA.** `$middleware->statefulApi()` en `bootstrap/app.php`,
  para que la SPA use la cookie de sesión. Comprueba que `install:api` dejó
  instalado Sanctum con `composer show laravel/sanctum`: usa el `composer` del
  PATH y, si falla, no lo dice.
- **El usuario** usa `Laravel\Sanctum\HasApiTokens` y `Notifiable`. Si implementa
  `MustVerifyEmail`, se envía el correo de verificación al registrarse. Si define
  `isAdmin()`, es quien puede suplantar a otros.

Para cambiar rutas, middleware, redirecciones o reglas de contraseña:

```bash
php artisan vendor:publish --tag=laravel-auth-config
```

## Uso desde una SPA

```js
axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true

await axios.get('/sanctum/csrf-cookie')
const { data } = await axios.post('/auth/login', { email, password, remember: true })
// data = { success: true, user: { ... } }

const auth = await axios.get('/auth/get-auth')
// { user, authenticated, is_admin, verified, impersonating }
```

Los errores de validación llegan como los de cualquier FormRequest de Laravel:
422 con `{ message, errors: { campo: [...] } }`.

## Rutas

Todas bajo el prefijo `auth` y el nombre `auth.`.

| Método | URI | Nombre | Middleware | Respuesta JSON |
|---|---|---|---|---|
| POST | `login` | `login` | guest | `{ success, user }`; 422 con credenciales inválidas o tras 5 intentos |
| POST | `register` | `register` | guest | `{ success, user }`; 403 si `allow-registration` es false |
| POST | `logout` | `logout` | auth:sanctum | `{ success }` |
| GET | `get-auth` | `get.auth` | — | `{ user, authenticated, is_admin, verified, impersonating }` |
| POST | `forgot-password` | `forgot.password` | guest | `{ success, message }`, igual exista o no la cuenta |
| POST | `reset-password` | `reset.password` | guest | `{ success, message }`; 422 con token inválido |
| POST | `update-password` | `update.password` | auth:sanctum | `{ success, message }`; 422 en `old_password` si no es la actual |
| POST | `email-verification-notification` | `email.verification.notification` | auth:sanctum, throttle | `{ success, status }` con `verification-link-sent` o `already-verified` |
| GET | `email/verify/{id}/{hash}` | `verification.verify` | auth:sanctum, signed, throttle | `{ verified }` |
| POST | `create-token` | `create.token` | throttle:6,1 | `{ token }`; 422 con credenciales inválidas |
| POST | `tokens` | `tokens` | auth:sanctum | `{ tokens }` |
| POST | `revoke-token` | `revoke.token` | auth:sanctum | `{ revoked }` |
| POST | `flush-tokens` | `flush.tokens` | auth:sanctum | `{ success, message }` |
| GET | `social/{provider}/redirect` | `socialite.redirect` | guest | redirige al proveedor; 404 si no está configurado |
| GET | `social/{provider}/callback` | `socialite.callback` | guest | entra o crea la cuenta y redirige |
| POST | `impersonate` | `impersonate` | auth:sanctum | `{ token, url }` |
| GET | `impersonate/{token}` | `impersonate.token` | auth:sanctum | `{ success, user }` o redirige |
| POST | `revert-impersonate` | `revert.impersonate` | auth:sanctum | `{ success, user }` o redirige; con token CSRF |

Cuando la petición no pide JSON, cada ruta redirige a `routes.redirects.*`.

## Contraseñas

```php
'password' => [
    'length' => 8,
    'uppercase' => false,
    'number' => false,
],
```

Se aplican al registrarse, al restablecer y al cambiar la contraseña. El mensaje
dice la regla que falla.

## Recuperar la contraseña

El correo enlaza a la pantalla de la aplicación que dice
`frontend.reset-password` (por omisión `auth/reset-password/{token}/{email}`),
con el correo codificado. Esa pantalla envía `token`, `email`, `password` y
`password_confirmation` a `POST /auth/reset-password`.

## Tokens

`POST /auth/create-token` con `email`, `password`, `name` y, opcionales,
`abilities` y `expires_at`. No inicia sesión: el token se usa como
`Authorization: Bearer <token>` en las rutas con `auth:sanctum`.

## Login social

Declara el proveedor en `config/services.php`:

```php
'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect' => '/auth/social/github/callback',
],
```

Un proveedor que no está ahí responde 404. La vuelta entra en la cuenta del
correo que devuelve el proveedor o la crea.

## Suplantar a un usuario

Sólo puede quien pasa la habilidad de Gate `laravel-auth.impersonate`. Por
omisión es un usuario cuyo modelo responde true a `isAdmin()`, nunca sobre sí
mismo ni sobre otro administrador. Para otra regla, redefínela en tu
`AuthServiceProvider`:

```php
Gate::define('laravel-auth.impersonate', fn ($user, $target = null) => $user->hasRole('soporte'));
```

El flujo:

1. `POST /auth/impersonate` con `target_user_id` devuelve `{ token, url }`.
2. Navegar a `url` inicia sesión como el usuario. El token sólo sirve a quien lo
   pidió y durante dos minutos.
3. `POST /auth/revert-impersonate`, con el token CSRF, vuelve a la cuenta
   original. Pasadas dos horas cierra la sesión en lugar de volver.

```js
// axios envía X-XSRF-TOKEN desde la cookie XSRF-TOKEN
axios.defaults.withXSRFToken = true

await axios.post('/auth/revert-impersonate')
```

```blade
<form method="POST" action="{{ route('auth.revert.impersonate') }}">
    @csrf
    <button>Volver a mi cuenta</button>
</form>
```

Poner `allow-impersonate` en false desactiva todo.

## Personalizar

```php
// Reglas del registro, por ejemplo para pedir más campos
RegisterRequest::setCustomRulesCallback(fn ($request) => [...]);

// Lo que responde get-auth
GetAuthRequest::$customGetAuthCallback = fn ($user) => response()->json([...]);

// Login social: entrar en una cuenta existente o registrar una nueva
CallbackRequest::$customLoginCallback = fn ($user, $provider, $providerUser) => ...;
CallbackRequest::$customRegisterCallback = fn ($providerUser, $provider) => ...;

// Sustituir por completo quién puede suplantar
ImpersonateRequest::authorizeUsing(fn ($request) => ...);
```

## Traducciones

Los mensajes son claves en inglés y el paquete trae `lang/es.json`. Para
corregirlos, añade la clave en el `lang/<idioma>.json` de la aplicación.

## Actualizar de 6.0 a 6.1

- **`revert-impersonate` sólo acepta POST.** Era un GET que cambiaba la cuenta de
  la sesión, y otro sitio lo disparaba con un `<img>` o un enlace sin pasar por el
  token CSRF. Cambia cada llamada de `GET` a `POST` con el token CSRF (ver
  «Suplantar a un usuario»). Un enlace `<a href="/auth/revert-impersonate">`
  tiene que ser un formulario o un botón que haga el POST.
- El nombre `auth.revert.impersonate`, la URI, el middleware y las respuestas no
  cambian.
- Un GET que quede sin cambiar ya no vuelve a la cuenta original: responde 405, o
  lo atiende el fallback de la aplicación si tiene uno, y la sesión sigue como
  estaba.

## Actualizar de 5.x a 6.0

- Las rutas de suplantación piden sesión, y sólo suplanta quien pasa
  `laravel-auth.impersonate`. Entrar con el token responde JSON o redirige; ya no
  hay vista `laravel-auth::impersonate`.
- Las reglas de contraseña van en `password`, en la raíz de la configuración
  (antes `routes.password`, donde nunca se leían).
- `forgot-password` responde lo mismo exista o no la cuenta.
- `reset-password` con token inválido responde 422.
- `update-password` con la contraseña actual equivocada responde un error de
  validación en `old_password`, no `{ error }`.
- `email-verification-notification` responde `{ success, status }`.
- `create-token` no inicia sesión, responde 422 con credenciales inválidas y
  admite seis intentos por minuto.
- `revoke-token` dice en `revoked` si se revocó algo.
- Las redirecciones por omisión van a `/admin`, `/auth/login` y `/`.

Si publicaste la configuración, compárala con la del paquete.

## Pruebas

```bash
composer install
vendor/bin/phpunit
```

## Autores ✒️

- Homero Raul Vargas Cruz

## Licencia 📄

Este paquete se encuentra bajo la licencia MIT. Esto significa que puedes usarlo de manera gratuita para cualquier propósito, incluso para propósitos comerciales. No obstante, hay algunas condiciones que debes tener en cuenta.

La licencia MIT te concede el derecho de usar, copiar, modificar, fusionar, publicar, distribuir, sublicenciar y/o vender copias del software. Sin embargo, en todos estos casos, debes proporcionar una copia del aviso de la licencia y el aviso de derechos de autor en todas las copias o partes sustanciales del software.

El software se proporciona "tal cual", sin garantía de ningún tipo, expresa o implícita, incluyendo pero no limitado a las garantías de comerciabilidad, aptitud para un propósito particular y no infracción. En ningún caso, los autores o los titulares de los derechos de autor serán responsables de cualquier reclamación, daños u otras responsabilidades, ya sea en una acción de contrato, agravio o de otro modo, que surjan de, estén en conexión con el software o el uso u otras operaciones en el software.

Para ver la licencia completa, por favor visita el archivo LICENSE incluido en este paquete o haz [click aquí](https://opensource.org/licenses/MIT) para verla en la página oficial de la licencia MIT en Open Source Initiative.

## Expresiones de Gratitud 🎁

Como en todos mis proyectos agradezco a mis padres, hermanos, esposa e hijos quienes son mi inspiración mi motor y razón de dar cada día siempre lo mejor de mi.

⌨️ con ❤️ por Homero Raul Vargas Cruz 😊

## Documentación / Documentation

Documentación completa del ecosistema, en español y en inglés / Full ecosystem documentation, in Spanish and English: <https://innoboxrr.github.io/docs/paquetes/laravel-auth>
