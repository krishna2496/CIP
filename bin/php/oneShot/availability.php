<?php

require_once('bootstrap/app.php');

$db = app()->make('db');

$db->connection('mysql')->getPdo();

$pdo = $db->connection('mysql')->getPdo();

\Illuminate\Support\Facades\Config::set('database.default', 'mysql');
$tenants = $pdo->query('select * from tenant where status=1')->fetchAll();

if (count($tenants) > 0) {
    foreach ($tenants as $tenant) {
        $tenantId = $tenant['tenant_id'];
        $db->purge('tenant');
        // Create connection to tenant
        \Illuminate\Support\Facades\Config::set('database.connections.tenant', array(
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'database' => 'ci_tenant_'.$tenantId,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ));
        // Create connection for the tenant database
        $pdo = $db->connection('tenant')->getPdo();
        
        // Set default database
        \Illuminate\Support\Facades\Config::set('database.default', 'tenant');

        $availabilities = $pdo->query('select * from availability')->fetchAll();
        if (!empty($availabilities)) {
            foreach ($availabilities as $availability) {
                $data = @unserialize($availability['translations']);
           
                if ($data !== false) {
                    $availabilityArray = unserialize($availability['translations']);
                    $jsonData  = json_encode($availabilityArray);

                    $pdo->prepare('
                        UPDATE availability
                        SET `translations` = :translations
                        WHERE availability_id = :availability_id
                    ')
                        ->execute([
                            'translations' => $jsonData,
                            'availability_id' => $availability['availability_id']
                        ]);
                }
            }
        }
    }
}