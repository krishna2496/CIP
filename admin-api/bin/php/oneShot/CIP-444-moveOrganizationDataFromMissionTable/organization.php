<?php

require_once('../../../../bootstrap/app.php');

$db = app()->make('db');

$pdo = $db->connection('mysql')->getPdo();

\Illuminate\Support\Facades\Config::set('database.default', 'mysql');
use Illuminate\Support\Str;
$tenants = $pdo->query('select * from tenant where status=1 AND deleted_at IS NULL')->fetchAll();

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

        $uniqueOrganizationDataFromMission = $pdo->query('select organisation_id ,organisation_name from mission GROUP BY organisation_name')->fetchAll();

        if (!empty($uniqueOrganizationDataFromMission)) {
            foreach ($uniqueOrganizationDataFromMission as $organizationData) {
                $id = (String) Str::uuid();
                $name = $organizationData['organisation_name'];
                $pdo->exec('SET NAMES utf8mb4');
                $pdo->exec('SET CHARACTER SET utf8mb4');

                $sql = $pdo->prepare("SELECT name FROM organization WHERE name=?");
                $sql->execute([$name]); 
                $getExistingOrganization = $sql->fetchAll();

                if(count($getExistingOrganization) === 0){
                    $pdo->prepare('
                        INSERT INTO organization (organization_id, name, created_at) VALUES
                        (:id, :name, :created_at)
                    ')
                    ->execute([
                        'id' => $id,
                        'name' => $name,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        $organizationTableData = $pdo->query('select organization_id, name  from organization')->fetchAll();

        if (!empty($organizationTableData)) {
            foreach ($organizationTableData as $organizationData) {

                $id = $organizationData['id'];
                $organizationName = $organizationData['name'];
                $pdo->prepare('
                    UPDATE mission
                    SET `organisation_id` = :id,
                    `updated_at` = :updated_at
                    WHERE organisation_name = :organizationName
                ')
                ->execute([
                    'id' => $id,
                    'organizationName' => $organizationName,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

    }
}