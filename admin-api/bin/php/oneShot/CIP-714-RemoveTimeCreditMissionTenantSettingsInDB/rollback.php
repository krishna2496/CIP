<?php

require_once(__DIR__.'/../../../../bootstrap/app.php');

$db = app()->make('db');

$adminDatabaseConnection = setAdminDatabaseConnection($db);
$pdo = $adminDatabaseConnection['pdo'];
$connection = $adminDatabaseConnection['connection'];

$tenantSetting = $pdo->query("
	SELECT tenant_setting_id FROM tenant_setting WHERE `key` = 'time_credit_mission'
")->fetch();

try {
	$connection->beginTransaction();

	$tenantHasSetting = $pdo->prepare("
		UPDATE tenant_has_setting SET deleted_at = ? WHERE `tenant_setting_id` = ?
	");
	$updateTenantHasSetting = $tenantHasSetting->execute([
		null,
		$tenantSetting['tenant_setting_id']
	]);
	
	if ($updateTenantHasSetting === true) {
		echo "Done reverting the update in the tenant_has_setting table... \n";
		echo "Will revert the update in tenant_setting table now... \n";

		$updateTenantSetting = $pdo->prepare("
			UPDATE tenant_setting SET deleted_at = ? WHERE `tenant_setting_id` = ? AND `key` = 'time_credit_mission'
		");
		$updated = $updateTenantSetting->execute([
			null,
			$tenantSetting['tenant_setting_id']
		]);

		if ($updated === true) {
			echo "Done reverting the update in the tenant_setting table now! \n";
		}
	}
	$connection->commit();
	
} catch (Exception $exception) {
	$connection->rollback();
	echo 'Failed: ' . $exception->getMessage() . "... \n";
}

function setAdminDatabaseConnection($db)
{
	$db->purge('mysql');

	// Create connection to db
    \Illuminate\Support\Facades\Config::set('database.connections.tenant', [
        'driver' => 'mysql',
        'host' => env('DB_HOST'),
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
    ]);

    // Create connection for the admin database
    $connection = $db->connection('mysql');
    $pdo = $connection->getPdo();

    // Set default database
    \Illuminate\Support\Facades\Config::set('database.default', 'mysql');

    return ['pdo' => $pdo, 'connection' => $connection];
}
