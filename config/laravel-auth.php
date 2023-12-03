<?php

return [

	'user-class' => 'App\\Models\\User',

	'allow-registration' => true,

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

			'socialite-callback' => 'socialite.callback'

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

			'create-token' => [],

			'tokens' => ['auth:sanctum'],

			'revoke-token' => ['auth:sanctum'],

			'flush-tokens' => ['auth:sanctum'],

			'socialite-redirect' => ['guest'],

			'socialite-callback' => ['guest'],

		],

		/**
		 * Especifica las redirecciones que debe resolver la aplicación tras
		 * la ejecución de las siguientes acciones
		 **/
		'redirects' => [

			'login' => '/home',

			'register' => '/home',

			'logout' => '/home',

			'reset-password' => '/login', // Ruta de autenticación

			'update-password' => '/home', // Ruta de autenticación

			'email-verification-notification' => '/home',

			'verification.verify' => '/home',

			'get-auth' => '/home',

			'create-token' => '/tokens',

			'tokens' => '/tokens',

			'revoke-token' => '/tokens',

			'flush-tokens' => '/tokens',

			'socialite-redirect' => '/', // Esto lo determina Socialite

			'socialite-callback' => '/',

		],

		'password' => [

			'length' => 8,

			'uppercase' => false,

			'number' => false,

		]

	],

];