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

        $newsCategories = $pdo->query('select news_category_id,translations from news_category')->fetchAll();
        if (!empty($newsCategories)) {
            foreach ($newsCategories as $newsCategory) {
                $data = @unserialize($newsCategory['translations']);
           
                if ($data !== false) {
                    $newsCategoryArray = unserialize($newsCategory['translations']);
                    $jsonData  = json_encode($newsCategoryArray);

                    $pdo->prepare('
                        UPDATE news_category
                        SET `translations` = :translations
                        WHERE news_category_id = :news_category_id
                    ')
                        ->execute([
                            'translations' => $jsonData,
                            'news_category_id' => $newsCategory['news_category_id']
                        ]);
                }
            }
        }
    }
}