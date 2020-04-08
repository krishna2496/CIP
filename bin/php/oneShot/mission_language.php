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

        $missionLanguages = $pdo->query('select * from mission_language')->fetchAll();
        if (!empty($missionLanguages)) {
            foreach ($missionLanguages as $missionLanguage) {
                //description
                $data = @unserialize($missionLanguage['description']);
           
                if ($data !== false) {
                    $missionLanguageArray = unserialize($missionLanguage['description']);
                    $jsonData  = json_encode($missionLanguageArray);

                    $pdo->prepare('
                        UPDATE mission_language
                        SET `description` = :description
                        WHERE mission_language_id = :id
                    ')
                        ->execute([
                            'description' => $jsonData,
                            'id' => $missionLanguage['mission_language_id']
                        ]);
                }
                //custom information
                $customInformationData = @unserialize($missionLanguage['custom_information']);
           
                if ($customInformationData !== false) {
                    $missionLanguageArray = unserialize($missionLanguage['custom_information']);
                    $jsonData  = json_encode($missionLanguageArray);

                    $pdo->prepare('
                        UPDATE mission_language
                        SET `custom_information` = :custom_information
                        WHERE mission_language_id = :id
                    ')
                        ->execute([
                            'custom_information' => $jsonData,
                            'id' => $missionLanguage['mission_language_id']
                        ]);
                }
            }
        }
    }
}