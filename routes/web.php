<?php

/*
|--------------------------------------------------------------------------
| Default route
|--------------------------------------------------------------------------
| This is default route of Laravel Lumen
|
*/

$router->get(
    '/',
    function () use ($router) {
        return $router->app->version();
    }
);

/*
|--------------------------------------------------------------------------
| Tenants Routes for super admin
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->group(
    ['prefix' => 'tenants', 'middleware' => 'localization'],
    function ($router) {
        // Get tenants list
        $router->get('/', ['as' => 'tenants.detail', 'uses'=>'TenantController@index']);
        // Get tenant details from id
        $router->get('/{tenant_id}', ['as' => 'tenants.detail', 'uses'=>'TenantController@show']);
        // Create new tenant
        $router->post('/', ['as' => 'tenants.store', 'uses'=>'TenantController@store']);
        // Update tenant details
        $router->patch('/{tenant_id}', ['as' => 'tenants.update', 'uses'=>'TenantController@update']);
        // Delete tenant
        $router->delete('/{tenant_id}', ['as' => 'tenants.destroy', 'uses'=>'TenantController@destroy']);
    }
);
