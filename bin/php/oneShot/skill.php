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

        $skills = $pdo->query('select * from skill')->fetchAll();
        if (!empty($skills)) {
            foreach ($skills as $skill) {
                $data = @unserialize($skill['translations']);
           
                if ($data !== false) {
                    $skillArray = unserialize($skill['translations']);
                    $jsonData  = json_encode($skillArray);

                    $pdo->prepare('
                        UPDATE skill
                        SET `translations` = :translations
                        WHERE skill_id = :skill_id
                    ')
                        ->execute([
                            'translations' => $jsonData,
                            'skill_id' => $skill['skill_id']
                        ]);
                }
            }
        }
    }
}