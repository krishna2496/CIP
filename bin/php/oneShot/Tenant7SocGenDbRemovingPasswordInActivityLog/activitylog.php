<?php

require_once('../../../../bootstrap/app.php');

$db = app()->make('db');

// Create connection to tenant 7
\Illuminate\Support\Facades\Config::set('database.connections.tenant', array(
    'driver' => 'mysql',
    'host' => env('DB_HOST'),
    'database' => 'ci_tenant_7',
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
));
// Create connection for the tenant database
$pdo = $db->connection('tenant')->getPdo();

// Set default database
\Illuminate\Support\Facades\Config::set('database.default', 'tenant');

$activitiesLog = $pdo->query(
    "select activity_log_id, object_value 
    from activity_log 
    where `type` like '%USER%' 
    or `type` like '%AUTH%'"
)
    ->fetchAll();

$logKeysToDelete = [
    'confirm_password',
    'old_password',
    'password',
    'password_confirmation'
];

foreach ($activitiesLog as $activityLog) {
    $id = $activityLog['activity_log_id'];
    $log = unserialize($activityLog['object_value']);

    if (is_array($log)) {
        $needModification = false;

        foreach ($logKeysToDelete as $logKeyToDelete) {
            if (array_key_exists($logKeyToDelete, $log)) {
                unset($log[$logKeyToDelete]);
                $needModification = true;
            }
        }

        if ($needModification) {
            $pdo->prepare('
                UPDATE activity_log
                SET `object_value` = :object_value
                WHERE `activity_log_id` = :id
            ')
                ->execute(['object_value' => serialize($log), 'id' => $id]);
        }
    }
}
