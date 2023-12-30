<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// AUTH

	Route::post(config('laravel-auth.routes.uris.login'), 'AuthenticationController@login')
		->middleware(config('laravel-auth.routes.middlewares.login'))
		->name(config('laravel-auth.routes.names.login'));

	Route::post(config('laravel-auth.routes.uris.register'), 'AuthenticationController@register')
		->middleware(config('laravel-auth.routes.middlewares.register'))
		->name(config('laravel-auth.routes.names.register'));

	Route::post(config('laravel-auth.routes.uris.logout'), 'AuthenticationController@logout')
		->middleware(config('laravel-auth.routes.middlewares.logout'))
		->name(config('laravel-auth.routes.names.logout'));

	Route::post(config('laravel-auth.routes.uris.forgot-password'), 'AuthenticationController@forgotPassword')
		->middleware(config('laravel-auth.routes.middlewares.forgot-password'))
		->name(config('laravel-auth.routes.names.forgot-password'));

	Route::post(config('laravel-auth.routes.uris.reset-password'), 'AuthenticationController@resetPassword')
		->middleware(config('laravel-auth.routes.middlewares.reset-password'))
		->name(config('laravel-auth.routes.names.reset-password'));

	Route::post(config('laravel-auth.routes.uris.update-password'), 'AuthenticationController@updatePassword')
		->middleware(config('laravel-auth.routes.middlewares.update-password'))
		->name(config('laravel-auth.routes.names.update-password'));

	Route::post(config('laravel-auth.routes.uris.email-verification-notification'), 'AuthenticationController@emailVerificationNotification')
		->middleware(config('laravel-auth.routes.middlewares.email-verification-notification'))
		->name(config('laravel-auth.routes.names.email-verification-notification'));

	Route::get(config('laravel-auth.routes.uris.get-auth'), 'AuthenticationController@getAuth')
		->middleware(config('laravel-auth.routes.middlewares.get-auth'))
		->name(config('laravel-auth.routes.names.get-auth'));

	Route::get(config('laravel-auth.routes.uris.verification-verify'), 'AuthenticationController@verificationVerify')
		->middleware(config('laravel-auth.routes.middlewares.verification-verify'))
		->name(config('laravel-auth.routes.names.verification-verify'));

// TOKEN

	Route::post(config('laravel-auth.routes.uris.create-token'), 'TokenController@createToken')
		->middleware(config('laravel-auth.routes.middlewares.create-token'))
		->name(config('laravel-auth.routes.names.create-token'));

	Route::post(config('laravel-auth.routes.uris.tokens'), 'TokenController@tokens')
		->middleware(config('laravel-auth.routes.middlewares.tokens'))
		->name(config('laravel-auth.routes.names.tokens'));

	Route::post(config('laravel-auth.routes.uris.revoke-token'), 'TokenController@revokeToken')
		->middleware(config('laravel-auth.routes.middlewares.revoke-token'))
		->name(config('laravel-auth.routes.names.revoke-token'));

	Route::post(config('laravel-auth.routes.uris.flush-tokens'), 'TokenController@flushTokens')
		->middleware(config('laravel-auth.routes.middlewares.flush-tokens'))
		->name(config('laravel-auth.routes.names.flush-tokens'));


// SOCIALITE

	Route::get(config('laravel-auth.routes.uris.socialite-redirect'), 'SocialiteController@redirect')
		->middleware(config('laravel-auth.routes.middlewares.socialite-redirect'))
		->name(config('laravel-auth.routes.names.socialite-redirect'));

	Route::get(config('laravel-auth.routes.uris.socialite-callback'), 'SocialiteController@callback')
		->middleware(config('laravel-auth.routes.middlewares.socialite-callback'))
		->name(config('laravel-auth.routes.names.socialite-callback'));

// Act As

	Route::post(config('laravel-auth.routes.uris.impersonate'), 'AuthenticationController@impersonate')
		->middleware(config('laravel-auth.routes.middlewares.impersonate'))
		->name(config('laravel-auth.routes.names.impersonate'));
	
	Route::get(config('laravel-auth.routes.uris.impersonate-token'), 'AuthenticationController@impersonateToken')
		->middleware(config('laravel-auth.routes.middlewares.impersonate-token'))
		->name(config('laravel-auth.routes.names.impersonate-token'));

	Route::get(config('laravel-auth.routes.uris.revert-impersonate'), 'AuthenticationController@revertImpersonate')
		->middleware(config('laravel-auth.routes.middlewares.revert-impersonate'))
		->name(config('laravel-auth.routes.names.revert-impersonate'));