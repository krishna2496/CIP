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

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/connect', ['middleware' => 'connect','uses' => 'TestController@index']);

/* user login routing using jwt token */
$router->post('login', ['uses' => 'AuthController@authenticate']);

/* user listing routing using middleware to verify token */
$router->group(
    ['middleware' => 'jwt.auth'], 
    function() use ($router) {
        $router->get('users', function() {
            $users = \App\User::all();
            return response()->json($users);
        });
    }
);

/*  forgot password routing */
$router->post('request_password_reset', ['uses' => 'AuthController@requestPasswordReset']);

/*  password reset routing */
$router->post('/reset_password/{token}', ['as' => 'password.reset', 'uses' => 'ResetPasswordController@reset']);

/*  get custom styling data for tenant specific */
$router->post('/custom_data', ['uses' => 'CustomController@customData']);


