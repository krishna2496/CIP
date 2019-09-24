<?php
namespace App\Repositories\TenantLanguage;

use App\Repositories\TenantLanguage\TenantLanguageInterface;
use Illuminate\Http\Request;
use App\Models\TenantLanguage;
use App\Models\Language;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TenantLanguageRepository implements TenantLanguageInterface
{
    /**
     * @var App\Models\TenantLanguage
     */
    private $tenantLanguage;

    /**
     * @var App\Models\Language
     */
    private $language;

    /**
     * Create a new tenant language repository instance.
     *
     * @param App\Models\TenantLanguage $tenantLanguage
     * @param App\Models\Language $language
     * @return void
     */
    public function __construct(TenantLanguage $tenantLanguage, Language $language)
    {
        $this->tenantLanguage = $tenantLanguage;
        $this->language = $language;
    }

    /**
     * Get tenant language lists.
     *
     * @param Illuminate\Http\Request $request
     * @param int $tenantId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getTenantLanguageList(Request $request, int $tenantId): LengthAwarePaginator
    {
        $tenantLanguageData = $this->tenantLanguage
        ->with(['language' => function ($query) {
            $query->select('language_id', 'name', 'code');
        }])->where('tenant_id', $tenantId)->paginate($request->perPage);

        foreach ($tenantLanguageData as $value) {
            $value->name = $value->language->name;
            $value->code = $value->language->code;
            unset($value->language);
        }

        return $tenantLanguageData;
    }

    /**
     * Store/Update tenant language data.
     *
     * @param  array $tenantLanguageData
     * @return App\Models\TenantLanguage
     */
    public function storeOrUpdate(array $tenantLanguageData): TenantLanguage
    {
        $condition = array('tenant_id' => $tenantLanguageData['tenant_id'],
        'language_id' => $tenantLanguageData['language_id']);

        if ($tenantLanguageData['default'] == config('constants.language_status.ACTIVE')) {
            $this->tenantLanguage->resetDefaultTenantLanguage($tenantLanguageData['tenant_id']);
        }
        // Check for deleted data
        $languageTrashedData = $this->tenantLanguage->where($condition)
        ->onlyTrashed()->first();
        if ($languageTrashedData) {
            $this->tenantLanguage->where($condition)->restore();
            $languageTrashedData->update(['default' => $tenantLanguageData['default']]);

            return $languageTrashedData;
        } else {
            return $this->tenantLanguage->createOrUpdate($condition, $tenantLanguageData);
        }
    }

    /**
     * Delete tenant language data.
     *
     * @param  int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $tenantLanguageData =  $this->tenantLanguage->findOrFail($id);
        return $tenantLanguageData->delete();
    }

    /**
     * Check language status.
     *
     * @param  int $id
     * @param  string $status
     * @return null|Collection
     */
    public function checkLanguageStatus(int $id, string $status): ?Collection
    {
        return $this->language->checkStatus($id, $status);
    }
}
