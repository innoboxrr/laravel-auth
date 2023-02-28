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

			'email-verification-notification' => 'email-verification-notification',

			'email-verification' => 'email-verification',

			'create-token' => 'create-token',

			'tokens' => 'tokens',

			'revoke-token' => 'revoke-token',

			'flush-tokens' => 'flush-tokens'

		],

		'names' => [

			'login' => 'login',

			'register' => 'register',

			'logout' => 'logout',

			'forgot-password' => 'forgot-password',

			'reset-password' => 'reset-password',

			'email-verification-notification' => 'email-verification-notification',

			'email-verification' => 'email-verification',

			'create-token' => 'create-token',

			'tokens' => 'tokens',

			'revoke-token' => 'revoke-token',

			'flush-tokens' => 'flush-tokens'

		],

		'middlewares' => [

			'login' => ['guest'],

			'register' => ['guest'],

			'logout' => ['auth:sanctum'],

			'forgot-password' => ['guest'],

			'reset-password' => ['guest'],

			'email-verification-notification' => ['auth:sanctum', 'throttle:6,1'],

			'email-verification' => ['auth:sanctum', 'signed', 'throttle:6,1'],

			'create-token' => ['auth:sanctum'],

			'tokens' => ['auth:sanctum'],

			'revoke-token' => ['auth:sanctum'],

			'flush-tokens' => ['auth:sanctum'],

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

			'email-verification-notification' => '/home',

			'create-token' => '/tokens',

			'tokens' => '/tokens',

			'revoke-token' => '/tokens',

			'flush-tokens' => '/tokens'

		],

	],

];