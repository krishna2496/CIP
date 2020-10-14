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

	$tenantHasSettingStatement = $pdo->prepare("
		SELECT * FROM tenant_has_setting WHERE `tenant_setting_id` = :tenant_setting_id
	");
	$tenantHasSettingStatement->bindParam(':tenant_setting_id', $tenantSetting['tenant_setting_id'], PDO::PARAM_STR);
	$tenantHasSettingStatement->execute();

	if (!empty($tenantHasSettingStatement->fetchAll())) {
		echo "The table `tenant_has_setting` is not empty, we will now revert the update on it... \n";

		$tenantHasSetting = $pdo->prepare("
			UPDATE tenant_has_setting SET deleted_at = ? WHERE `tenant_setting_id` = ?
		");
		$tenantHasSetting = $tenantHasSetting->execute([
			null,
			$tenantSetting['tenant_setting_id']
		]);
		if ($tenantHasSetting === true) {
			echo "Done reverting the update on the `tenant_has_setting` table! \n";
		}
	}

	echo "We will revert the update on the `tenant_setting` table now... \n";

	$updateTenantSetting = $pdo->prepare("
		UPDATE tenant_setting SET deleted_at = ? WHERE `tenant_setting_id` = ? AND `key` = 'time_credit_mission'
	");
	$updateTenantSetting = $updateTenantSetting->execute([
		null,
		$tenantSetting['tenant_setting_id']
	]);
	if ($updateTenantSetting === true) {
		echo "Done reverting the update on the `tenant_setting` table! \n";
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
