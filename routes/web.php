<?php

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
        $router->get('/', ['as' => 'tenants', 'uses'=>'TenantController@index']);
        // Get tenant details from id
        $router->get('/{tenant_id:[0-9]+}', ['as' => 'tenants.detail', 'uses'=>'TenantController@show']);
        // Create new tenant
        $router->post('/', ['as' => 'tenants.store', 'uses'=>'TenantController@store']);
        // Update tenant details
        $router->patch('/{tenant_id}', ['as' => 'tenants.update', 'uses'=>'TenantController@update']);
        // Delete tenant
        $router->delete('/{tenant_id}', ['as' => 'tenants.destroy', 'uses'=>'TenantController@destroy']);
        // Get api user list
        $router->get(
            '/{tenant_id}/api_users',
            ['as' => 'tenants.api-users',
            'uses' => 'TenantController@getAllApiUser']
        );
        // Get api user detail from id
        $router->get(
            '/{tenant_id}/api_users/{api_user_id}',
            ['as' => 'tenants.get-api-user',
            'uses' => 'TenantController@getApiUserDetail']
        );
        // create api user
        $router->post(
            '/{tenant_id}/api_users',
            ['as' => 'tenants.create-api-user',
            'uses' => 'TenantController@createApiUser']
        );
        // Regenarate api keys
        $router->patch(
            '/{tenant_id}/api_users/{api_user_id}',
            ['as' => 'tenants.renew-api-user',
            'uses' => 'TenantController@renewApiUser']
        );
        // Delete api user
        $router->delete(
            '/{tenant_id}/api_users/{api_user_id}',
            ['as' => 'tenants.delete-api-user',
            'uses' => 'TenantController@deleteApiUser']
        );
    }
);
