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
$router->group(['middleware' => 'localization'], function ($router) {
    /* Connect first time to get styling data. */
    $router->get('connect', ['middleware' => 'tenant.connection', 'uses' => 'App\Tenant\TenantOptionController@getTenantOption']);

    /* User login routing using jwt token */
    $router->post('login', ['middleware' => 'tenant.connection', 'uses' => 'App\Auth\AuthController@authenticate']);

    /* Forgot password routing */
    $router->post('request_password_reset', ['middleware' => 'tenant.connection','uses' => 'App\Auth\AuthController@requestPasswordReset']);

    /* Password reset routing */
    $router->post('/reset-password/{token}', ['as' => 'password.reset', 'uses' => 'App\Auth\AuthController@reset_password']);

    /* reset password  */
    $router->put('/password_reset', ['middleware' => 'localization|tenant.connection','uses' => 'App\Auth\AuthController@passwordReset']);

    /* CMS footer pages  */
    $router->get('/cms/listing', ['middleware' => 'localization|tenant.connection','uses' => 'App\Cms\CmsController@index']);
    $router->get('/cms/detail', ['middleware' => 'localization|tenant.connection','uses' => 'App\Cms\CmsController@cmsList']);
    $router->get('/cms/{pageId}', ['middleware' => 'localization|tenant.connection','uses' => 'App\Cms\CmsController@show']);

    /* Get custom field data  */
    $router->get('/custom_field/', ['middleware' => 'localization|tenant.connection','uses' => 'App\USer\UserCustomFieldController@index']);

    /* Get mission listing  */
    $router->get('/app/missions/', ['middleware' => 'localization|tenant.connection|jwt.auth','uses' => 'App\Mission\MissionController@appMissionList']);
});


/* Fetch Language json file */
$router->get('language/{lang}', ['uses' => 'App\Language\LanguageController@fetchLangaugeFile']);

/*
|
|--------------------------------------------------------------------------
| Tenant Admin Routs
|--------------------------------------------------------------------------
|
| These are tenant admin routes to manage tenant users, settings, and etc.
|
*/

/* Set user data for tenant specific */
$router->group(['prefix' => 'users', 'middleware' => 'localization|auth.tenant.admin'], function ($router) {
    $router->get('/', ['uses' => 'Admin\User\UserController@index']);
    $router->get('/{userId}', ['uses' => 'Admin\User\UserController@show']);
    $router->post('/', ['uses' => 'Admin\User\UserController@store']);
    $router->patch('/{userId}', ['uses' => 'Admin\User\UserController@update']);
    $router->delete('/{userId}', ['uses' => 'Admin\User\UserController@destroy']);
});

/* Set custom slider data for tenant specific */
$router->post('/create_slider', ['middleware' => 'localization|auth.tenant.admin', 'uses' => 'Admin\Tenant\TenantOptionsController@storeSlider']);

/* Set Footer Page data for tenant specific */
$router->group(['prefix' => 'cms', 'middleware' => 'localization|auth.tenant.admin'], function($router){
	$router->get('/', ['uses' => 'Admin\FooterPage\FooterPageController@index']);
	$router->post('/', ['uses' => 'Admin\FooterPage\FooterPageController@store']);
	$router->patch('/{pageId}', ['uses' => 'Admin\FooterPage\FooterPageController@update']);
	$router->delete('/{pageId}', ['uses' => 'Admin\FooterPage\FooterPageController@destroy']);
});

/* Set custom field data for tenant specific */
$router->group(['prefix' => 'metadata/users/custom_fields', 'middleware' => 'localization|auth.tenant.admin'], function ($router) {
    $router->get('/', ['uses' => 'Admin\User\UserCustomFieldController@index']);
    $router->post('/', ['uses' => 'Admin\User\UserCustomFieldController@store']);
    $router->patch('/{fieldId}', ['uses' => 'Admin\User\UserCustomFieldController@update']);
    $router->delete('/{fieldId}', ['uses' => 'Admin\User\UserCustomFieldController@destroy']);
});

/* Set mission data for tenant specific */
$router->group(['prefix' => 'missions', 'middleware' => 'localization|auth.tenant.admin'], function($router){ 
    $router->get('', ['uses' => 'Admin\Mission\MissionController@index']); 
    $router->post('/', ['uses' => 'Admin\Mission\MissionController@store']);  
    $router->patch('/{missionId}', ['uses' => 'Admin\Mission\MissionController@update']); 
    $router->delete('/{missionId}', ['uses' => 'Admin\Mission\MissionController@destroy']); 
    $router->get('/{missionId}/applications', ['uses' => 'Admin\Mission\MissionController@missionApplications']); 
    $router->get('/{missionId}/applications/{applicationId}', ['uses' => 'Admin\Mission\MissionController@missionApplication']); 
    $router->patch('/{missionId}/applications/{applicationId}', ['uses' => 'Admin\Mission\MissionController@updateApplication']); 

});

/* Set skill data for tenant user specific */
$router->group(['prefix' => 'entities/skills', 'middleware' => 'localization|auth.tenant.admin'], function ($router) {
    $router->get('/{userId}', ['uses' => 'Admin\User\UserController@userSkills']);
    $router->post('/', ['uses' => 'Admin\User\UserController@linkSkill']);
    $router->delete('/', ['uses' => 'Admin\User\UserController@unlinkSkill']);
});

/*Admin style routes*/
$router->group(['prefix' => 'style', 'middleware' => 'localization|auth.tenant.admin'], function ($router) {
    $router->post('/update-style', ['uses' => 'Admin\Tenant\TenantOptionsController@updateStyleSettings']);
    $router->get('/reset-style', ['uses' => 'Admin\Tenant\TenantOptionsController@resetStyleSettings']);
});

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