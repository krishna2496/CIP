<?php

require_once('bootstrap/app.php');

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class UpdateUserLanguageId
{
    private $db;

    public function __construct()
    {
        $this->db = app()->make('db');
    }

    /**
     * Run on shot script
     */
    public function run()
    {
        $tenants = $this->getTenantsDefaultLanguages();
        DB::transaction(function () use ($tenants) {
            $processed = [];
            foreach ($tenants as $tenant) {
                // Set connection for the tenant
                $this->createConnection($tenant->tenant_id);
                $users = $this->getTenantUsersWithoutLanguage();
                if (!$users->isEmpty()) {
                    $userIds = $users->pluck('user_id')->toArray();
                    $updated = $this->updateUsersLanguage($userIds, $tenant->language_id);
                    if ($updated) {
                        $processed['success'][$tenant->language_id] = $userIds;
                    } else {
                        $processed['failed'][$tenant->language_id] = $userIds;
                    }
                }
            }
            $this->logProcess($processed);
        });
    }

    /**
     * Log script process datas
     *
     * @param array $processed
     *
     * @return void
     */
    private function logProcess(array $processed)
    {
        echo "\nDone updating tenants users with empty language id.\n";
        foreach ($processed as $status => $items) {
            echo "\n".ucfirst($status)." items:\n";
            foreach ($items as $tenantId => $userIds) {
                echo "\nTenant ID: $tenantId\n";
                echo "User IDs: ".implode(', ', $userIds)."\n\n";
            }
        }
    }

    /**
     * Update users language per user ids
     *
     * @param array $ids
     * @param int $languageId
     *
     * @return bool
     */
    private function updateUsersLanguage(array $ids, $languageId): bool
    {
        return DB::table('user AS u')
            ->whereIn('u.user_id', $ids)
            ->update([
                'language_id' => $languageId
            ]);
    }

    /**
     * Get tenant users without language id
     *
     * @return Collection
     */
    private function getTenantUsersWithoutLanguage(): Collection
    {
        return DB::table('user AS u')
            ->selectRaw('
                u.user_id
            ')
            ->whereNull('u.language_id')
            ->get();
    }

    /**
     * Get tenants default languages
     *
     * @return Collection
     */
    private function getTenantsDefaultLanguages(): Collection
    {
        return DB::table('tenant AS t')
            ->selectRaw('
                t.tenant_id,
                tl.language_id
            ')
            ->join('tenant_language AS tl', function ($join) {
                $join->on('tl.tenant_id', '=', 't.tenant_id')
                    ->where('tl.default', '1');
            })
            ->get();
    }

    /**
     * Create database connection runtime
     *
     * @param int $tenantId
     */
    private function createConnection(int $tenantId)
    {
        $this->db->purge('tenant');
        Config::set('database.connections.tenant', array(
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'database' => 'ci_tenant_' . $tenantId,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ));
        // Set default database
        Config::set('database.default', 'tenant');
    }
}

return (new UpdateUserLanguageId())->run();