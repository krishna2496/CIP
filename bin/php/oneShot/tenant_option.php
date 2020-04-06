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

        $tenantOptions = $pdo->query('select * from tenant_option')->fetchAll();
        if (!empty($tenantOptions)) {
            foreach ($tenantOptions as $tenantOption) {
                $data = @unserialize($tenantOption['option_value']);
           
                if ($data !== false) {
                    $tenantOptionArray = unserialize($tenantOption['option_value']);
                    $jsonData  = json_encode($tenantOptionArray);

                    $pdo->prepare('
                        UPDATE tenant_option
                        SET `option_value` = :option_value
                        WHERE tenant_option_id = :id
                    ')
                        ->execute([
                            'option_value' => $jsonData,
                            'id' => $tenantOption['tenant_option_id']
                        ]);
                }
            }
        }
    }
}