<?php

/*
|--------------------------------------------------------------------------
| Default route
|--------------------------------------------------------------------------
| This is default route of Laravel Lumen
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/*
|--------------------------------------------------------------------------
| Authentication routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/* Connect first time to get styling data. */
$router->get('connect', ['middleware' => 'tenant.connection', 'uses' => 'App\Styling\StylingController@index']);

/* User login routing using jwt token */
$router->post('login', ['middleware' => 'tenant.connection', 'uses' => 'App\Auth\AuthController@authenticate']);

/* Forgot password routing */
$router->post('request_password_reset', ['middleware' => 'tenant.connection','uses' => 'App\Auth\AuthController@requestPasswordReset']);

/* Password reset routing */
$router->post('/reset_password/{token}', ['as' => 'password.reset', 'uses' => 'App\Auth\ResetPasswordController@reset']);

/* Get custom styling data for tenant specific */
$router->post('/custom_data', ['uses' => 'CustomController@customData']);

/*
|
|--------------------------------------------------------------------------
| Tenant User Routs
|--------------------------------------------------------------------------
|
| These are tenant user routes to manage their profile and other stuff
|
*/
/*$router->group(['middleware' => 'tenant.connection|jwt.auth'], function() use ($router) {
	$router->get('users', function() {
        $users = \App\User::all();
        return response()->json($users);
    });
});*/

/*
|
|--------------------------------------------------------------------------
| Tenant Admin Routs
|--------------------------------------------------------------------------
|
| These are tenant admin routes to manage tenant users, settings, and etc.
|
*/
$router->group(['prefix' => 'users', 'middleware' => 'auth.tenant.admin'], function($router){
	/* Get all users of tenant */
	$router->get('/', ['uses' => 'Admin\User\UserController@index']);
	$router->post('/create', ['uses' => 'Admin\User\UserController@store']);
	$router->delete('/{userId}', ['uses' => 'Admin\User\UserController@destroy']);
});

/* Set custom slider data for tenant specific */
$router->post('/create_slider', ['middleware' => 'auth.tenant.admin', 'uses' => 'Admin\Tenant\TenantOptionsController@storeSlider']);
/* Set cms data for tenant specific */
$router->post('/create', ['middleware' => 'auth.tenant.admin', 'uses' => 'Admin\Tenant\CmsController@store']);