<?php

require_once __DIR__.'/../../../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__.'/../../../')
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__.'/../../../')
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

// $app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);

/**
 * mailer package registration
 */
$app->register(Illuminate\Notifications\NotificationServiceProvider::class);
$app->register(\Illuminate\Mail\MailServiceProvider::class);
$app->register(\LaravelMandrill\MandrillServiceProvider::class);
$app->alias('mailer', \Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', \Illuminateminate\Mail\Mailer::class);
$app->alias('mailer', \Illuminate\Contracts\Mail\MailQueue::class);
$app->alias('mail.manager', Illuminate\Mail\MailManager::class);
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);

return $app;
