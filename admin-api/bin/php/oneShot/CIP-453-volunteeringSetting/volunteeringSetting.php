<?php

require_once('../../../../bootstrap/app.php');

$db = app()->make('db');

$pdo = setAdminDatabaseConnection($db);

// Get all the tenants id
$tenants = $pdo->query('select tenant_id from tenant where deleted_at is null')->fetchAll();
$tenantSettingIdData = $pdo->query('select tenant_setting_id from tenant_setting where title = "volunteering" AND  deleted_at is null')->fetchAll();

if (isset($tenantSettingIdData[0])) {
    $tenantSettingId = $tenantSettingIdData[0]['tenant_setting_id'];
} else {
    echo "Tenant setting is not exist in system." ;
    exit;
}

foreach ($tenants as $tenant) {
    $tenantId = $tenant['tenant_id'];
    $params['settings'] = [
        'tenant_setting_id' => $tenantSettingId,
        'value' => 1,
    ];

    $pdo = setAdminDatabaseConnection($db);

    // Add settings into tenant_has_setting (master database)
    $tenantHasSettingSql = $pdo->prepare("SELECT * FROM tenant_has_setting WHERE tenant_id=? AND tenant_setting_id=? AND deleted_at is null");
    $tenantHasSettingSql->execute([$tenantId, $tenantSettingId]);
    $getNewlyInsertedSettingId = $tenantHasSettingSql->fetchAll();

    if (count($getNewlyInsertedSettingId) === 0) {
        $pdo->prepare('
            INSERT INTO tenant_has_setting
            (tenant_id, tenant_setting_id, created_at)
            VALUES (:tenant_id, :tenant_setting_id, :created_at)
        ')
        ->execute([
            'tenant_id' => $tenantId,
            'tenant_setting_id' => $tenantSettingId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

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

    // Add settings into tenant_setting (tenant's database)
    $tenantSettingSql = $pdo->prepare("SELECT * FROM tenant_setting WHERE setting_id=? AND deleted_at is null");
    $tenantSettingSql->execute([$tenantSettingId]);
    $getExistingTenantSettingId = $tenantSettingSql->fetchAll();

    if (count($getExistingTenantSettingId) === 0) {
        $pdo->prepare('
            INSERT INTO tenant_setting
            (setting_id, created_at)
            VALUES (:setting_id, :created_at)
        ')
        ->execute([
            'setting_id' => $tenantSettingId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    //Activate volunteering setting in tenant_activated_setting (tenant's database)
    $sql = $pdo->prepare("SELECT tenant_setting_id FROM tenant_setting WHERE setting_id=?");
    $sql->execute([$tenantSettingId]);
    $getNewlyInsertedSettingId = $sql->fetchAll();

    $settingIdFromTenantSetting = $getNewlyInsertedSettingId[0]['tenant_setting_id'];

    $tenantActivatedSettingSql = $pdo->prepare("SELECT * FROM tenant_activated_setting WHERE tenant_setting_id=? AND deleted_at is null");
    $tenantActivatedSettingSql->execute([$settingIdFromTenantSetting]);
    $getActivatedTenantSettingId = $tenantActivatedSettingSql->fetchAll();

    if (count($getActivatedTenantSettingId) === 0) {
        $pdo->prepare('
            INSERT INTO tenant_activated_setting
            (tenant_setting_id, created_at)
            VALUES (:tenant_setting_id, :created_at)
        ')
        ->execute([
            'tenant_setting_id' => $settingIdFromTenantSetting,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}

function setAdminDatabaseConnection($db)
{
    $db->purge('mysql');

    // Create connection to db
    \Illuminate\Support\Facades\Config::set('database.connections.tenant', array(
        'driver' => 'mysql',
        'host' => env('DB_HOST'),
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
    ));
    
    // Create connection for the admin database
    $pdo = $db->connection('mysql')->getPdo();
    
    // Set default database
    \Illuminate\Support\Facades\Config::set('database.default', 'mysql');

    return $pdo;
}
