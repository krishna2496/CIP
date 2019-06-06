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
$router->group(['middleware' => 'localization'], function($router){
	/* Connect first time to get styling data. */
	$router->get('connect', ['middleware' => 'tenant.connection', 'uses' => 'App\Tenant\TenantOptionController@getTenantOption']);

	/* User login routing using jwt token */
	$router->post('login', ['middleware' => 'tenant.connection', 'uses' => 'App\Auth\AuthController@authenticate']);

	/* Forgot password routing */
	$router->post('request_password_reset', ['middleware' => 'tenant.connection','uses' => 'App\Auth\AuthController@requestPasswordReset']);

	/* Password reset routing */
	$router->post('/reset-password/{token}', ['as' => 'password.reset', 'uses' => 'App\Auth\AuthController@reset_password']);

	/* reset password  */
	$router->put('/password_reset', ['middleware' => 'tenant.connection','uses' => 'App\Auth\AuthController@passwordReset']);

});

/* Get CMS footer pages  */
$router->group(['prefix' => 'cms', 'middleware' => 'localization|tenant.connection'], function($router){
	$router->get('/', ['uses' => 'App\Cms\CmsController@index']);
	$router->get('/detail', ['uses' => 'App\Cms\CmsController@cmsList']);
	$router->get('/{slug}', ['uses' => 'App\Cms\CmsController@show']);
});


/* Get custom field data  */
$router->get('/custom_field/', ['middleware' => 'localization|tenant.connection','uses' => 'App\USer\UserCustomFieldController@index']);

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

/* Set cms data for tenant specific */
$router->group(['prefix' => 'users', 'middleware' => 'localization|auth.tenant.admin'], function($router){
	$router->get('/', ['uses' => 'Admin\User\UserController@index']);
	$router->post('/create', ['uses' => 'Admin\User\UserController@store']);
	$router->delete('/{userId}', ['uses' => 'Admin\User\UserController@destroy']);
});

/* Set custom slider data for tenant specific */
$router->post('/create_slider', ['middleware' => 'localization|auth.tenant.admin', 'uses' => 'Admin\Tenant\TenantOptionsController@storeSlider']);

/* Set cms data for tenant specific */
$router->group(['prefix' => 'cms', 'middleware' => 'localization|auth.tenant.admin'], function($router){
	$router->get('/', ['uses' => 'Admin\Cms\CmsController@index']);
	$router->post('/create', ['uses' => 'Admin\Cms\CmsController@store']);
	$router->patch('/update/{pageId}', ['uses' => 'Admin\Cms\CmsController@update']);
	$router->patch('/update/', ['uses' => 'Admin\Cms\CmsController@handleError']);
	$router->delete('/{pageId}', ['uses' => 'Admin\Cms\CmsController@destroy']);
	$router->delete('/', ['uses' => 'Admin\Cms\CmsController@handleError']);
});

/* Set custom field data for tenant specific */
$router->group(['prefix' => 'metadata/users/custom_fields', 'middleware' => 'localization|auth.tenant.admin'], function($router){ 
	$router->get('/', ['uses' => 'Admin\User\UserCustomFieldController@index']);
	$router->post('/create', ['uses' => 'Admin\User\UserCustomFieldController@store']);
	$router->patch('/{fieldId}', ['uses' => 'Admin\User\UserCustomFieldController@update']);
	$router->patch('/', ['uses' => 'Admin\User\UserCustomFieldController@handleError']);	
	$router->delete('/{fieldId}', ['uses' => 'Admin\User\UserCustomFieldController@destroy']);
	$router->delete('/', ['uses' => 'Admin\User\UserCustomFieldController@handleError']);
});
