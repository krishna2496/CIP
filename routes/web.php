<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// Get tenants list
$router->get('/tenants',['uses'=>'TenantController@index']);
// Get tenant details from id
$router->get('/tenants/{tenant_id}',['uses'=>'TenantController@show']);
// Create new tenant
$router->post('/tenant/create',['uses'=>'TenantController@store']);