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

        $userFilters = $pdo->query('select filters,user_filter_id from user_filter')->fetchAll();
        if (!empty($userFilters)) {
            foreach ($userFilters as $userFilter) {
                $data = @unserialize($userFilter['filters']);
           
                if ($data !== false) {
                    $userFilterArray = unserialize($userFilter['filters']);
                    $jsonData  = json_encode($userFilterArray);

                    $pdo->prepare('
                        UPDATE user_filter
                        SET `filters` = :filters
                        WHERE user_filter_id = :id
                    ')
                        ->execute([
                            'filters' => $jsonData,
                            'id' => $userFilter['user_filter_id']
                        ]);
                }
            }
        }
    }
}