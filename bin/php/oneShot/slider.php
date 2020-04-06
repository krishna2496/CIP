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

        $tenantOptions = $pdo->query('select * from slider')->fetchAll();
        if (!empty($tenantOptions)) {
            foreach ($tenantOptions as $tenantOption) {
                $data = @unserialize($tenantOption['translations']);
           
                if ($data !== false) {
                    $tenantOptionArray = unserialize($tenantOption['translations']);
                    $jsonData  = json_encode($tenantOptionArray);

                    $pdo->prepare('
                        UPDATE slider
                        SET `translations` = :translations
                        WHERE slider_id = :id
                    ')
                        ->execute([
                            'translations' => $jsonData,
                            'id' => $tenantOption['slider_id']
                        ]);
                }
            }
        }
    }
}