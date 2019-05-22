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
$router->group(['prefix' => 'tenants'], function($router){
	// Get tenants list
	$router->get('/',['uses'=>'TenantController@index']);
	// Get tenant details from id
	$router->get('/{tenant_id}',['uses'=>'TenantController@show']);
	// Create new tenant
	$router->post('/create',['uses'=>'TenantController@store']);
});