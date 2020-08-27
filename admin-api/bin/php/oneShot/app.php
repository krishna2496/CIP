<?php

require_once __DIR__.'/../../../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    realpath(__DIR__.'/../../../')
))->bootstrap();

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../../../')
);

$app->withFacades();
$app->withEloquent();

$app->configure('app');
$app->configure('database');
$app->configure('errors');
$app->configure('messages');
$app->configure('constants'); //constant file config
$app->configure('filesystems');
$app->configure('queue');
$app->configure('mail');
$app->configure('services');

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);

return $app;
