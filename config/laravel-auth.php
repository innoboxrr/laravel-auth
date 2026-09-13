<?php

return [

	'user-class' => 'App\\Models\\User',

	'allow-registration' => true,

	/**
	 * Permite entrar como otro usuario. Aun activado, sólo puede quien pasa la
	 * habilidad de Gate `laravel-auth.impersonate`: por omisión, un usuario
	 * cuyo modelo responde true a isAdmin(), y nunca sobre sí mismo ni sobre
	 * otro administrador. La aplicación la redefine en su AuthServiceProvider.
	 */
	'allow-impersonate' => true,

	/**
	 * Lo que se exige a una contraseña nueva, al registrarse, restablecerla o
	 * cambiarla.
	 */
	'password' => [

		'length' => 8,

		'uppercase' => false,

		'number' => false,

	],

	/**
	 * Pantallas de la aplicación a las que enlazan los correos del paquete.
	 */
	'frontend' => [

		// El enlace del correo de restablecer contraseña. El correo va
		// codificado para URL.
		'reset-password' => 'auth/reset-password/{token}/{email}',

	],

	'routes' => [

		/**
		 * Determina si las rutas de autenticación del paquete estarán disponibles
		 * Es decir habilita su registro en RouteServiceProvider
		 **/
		'active' => true,

		'as' => 'auth.',

		'prefix' => 'auth',

		'uris' => [

			'login' => 'login',

			'register' => 'register',

			'logout' => 'logout',

			'forgot-password' => 'forgot-password',

			'reset-password' => 'reset-password',

			'update-password' => 'update-password',

			'email-verification-notification' => 'email-verification-notification',

			'get-auth' => 'get-auth',
			
			'verification-verify' => 'email/verify/{id}/{hash}',

			'create-token' => 'create-token',

			'tokens' => 'tokens',

			'revoke-token' => 'revoke-token',

			'flush-tokens' => 'flush-tokens',

			'socialite-redirect' => 'social/{provider}/redirect',

			'socialite-callback' => 'social/{provider}/callback',

			'impersonate' => 'impersonate',

			'impersonate-token' => 'impersonate/{token}',

			'revert-impersonate' => 'revert-impersonate',

		],

		'names' => [

			'login' => 'login',

			'register' => 'register',

			'logout' => 'logout',

			'forgot-password' => 'forgot.password',

			'reset-password' => 'reset.password',

			'update-password' => 'update.password',

			'email-verification-notification' => 'email.verification.notification',

			'verification-verify' => 'verification.verify',

			'get-auth' => 'get.auth',

			'create-token' => 'create.token',

			'tokens' => 'tokens',

			'revoke-token' => 'revoke.token',

			'flush-tokens' => 'flush.tokens',

			'socialite-redirect' => 'socialite.redirect',

			'socialite-callback' => 'socialite.callback',

			'impersonate' => 'impersonate',

			'impersonate-token' => 'impersonate.token', 

			'revert-impersonate' => 'revert.impersonate',

		],

		'middlewares' => [

			'login' => ['guest'],

			'register' => ['guest'],

			'logout' => ['auth:sanctum'],

			'forgot-password' => ['guest'],

			'reset-password' => ['guest'],

			'update-password' => ['auth:sanctum'],

			'email-verification-notification' => ['auth:sanctum', 'throttle:6,1'],

			'verification-verify' => ['auth:sanctum', 'signed', 'throttle:6,1'],

			'get-auth' => [],

			// Recibe correo y contraseña: sin freno, sirve para probar
			// contraseñas por fuerza bruta.
			'create-token' => ['throttle:6,1'],

			'tokens' => ['auth:sanctum'],

			'revoke-token' => ['auth:sanctum'],

			'flush-tokens' => ['auth:sanctum'],

			'socialite-redirect' => ['guest'],

			'socialite-callback' => ['guest'],

			// Sin sesión no se suplanta a nadie. Quién puede, lo decide la
			// habilidad `laravel-auth.impersonate` (ver allow-impersonate).
			'impersonate' => ['auth:sanctum'],

			'impersonate-token' => ['auth:sanctum'],

			'revert-impersonate' => ['auth:sanctum'],

		],

		/**
		 * Especifica las redirecciones que debe resolver la aplicación tras
		 * la ejecución de las siguientes acciones
		 **/
		'redirects' => [

			// Sólo cuando la petición no pide JSON. Apuntan a las pantallas de la
			// aplicación que monta innoboxrr/laravel-setup: el administrador en
			// /admin y el acceso en /auth. Antes iban a /home y /tokens, que no
			// existen en ninguna.

			'login' => '/admin',

			'register' => '/admin',

			'logout' => '/',

			'reset-password' => '/auth/login',

			'update-password' => '/admin',

			'email-verification-notification' => '/admin',

			'verification-verify' => '/admin',

			'get-auth' => '/',

			'create-token' => '/',

			'tokens' => '/',

			'revoke-token' => '/',

			'flush-tokens' => '/',

			'socialite-redirect' => '/', // Esto lo determina Socialite

			'socialite-callback' => '/admin',

			'impersonate' => '/',

			'impersonate-token' => '/admin',

			'revert-impersonate' => '/admin',

		],

		/**
		 * Obsoleto: las reglas de contraseña van en `password`, arriba. Aquí
		 * nunca se leyeron; se siguen respetando si una configuración
		 * publicada todavía las tiene en este sitio.
		 */

	],

];