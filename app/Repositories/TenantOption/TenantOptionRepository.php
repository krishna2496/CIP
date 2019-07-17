<?php
namespace App\Repositories\TenantOption;

use App\Repositories\TenantOption\TenantOptionInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Validator;
use PDOException;
use DB;
use App\Models\TenantOption;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TenantOptionRepository implements TenantOptionInterface
{
    /**
     * The tenantOption for the model.
     *
     * @var App\Models\TenantOption
     */
    public $tenantOption;
    
    public function __construct(TenantOption $tenantOption)
    {
        $this->tenantOption = $tenantOption;
    }
    
    /**
    * Get a listing of slider.
    *
    * @return Illiminate\Support\Collection
    */
    public function getAllSlider(): Collection
    {
        return $this->tenantOption->where('option_name', config('constants.TENANT_OPTION_SLIDER'))->get();
    }

    /**
     * Update style settings.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function updateStyleSettings(Request $request)
    {
        if ($request->primary_color !== '') {
            $styleData['option_name'] = 'primary_color';
            $styleData['option_value'] = $request->primary_color;
            $this->tenantOption->addOrUpdateColor($styleData);
        }

        if ($request->secondary_color !== '') {
            $styleData['option_name'] = 'secondary_color';
            $styleData['option_value'] = $request->secondary_color;
            $this->tenantOption->addOrUpdateColor($styleData);
        }
    }

    /**
     * Store tenant slider data.
     *
     * @param  array $data
     * @return App\Models\TenantOption
     */
    public function storeSlider(array $data): TenantOption
    {
        return $this->tenantOption->create($data);
    }

    /**
     * Store tenant option data
     *
     * @return Illuminate\Support\Collection
     */
    public function getOptions(): Collection
    {
        return $this->tenantOption->get(['option_name', 'option_value']);
    }

    /**
    * Get a listing of resource.
    *
    * @param array $conditions
    * @return App\Models\TenantOption | Null
    */
    public function getOptionWithCondition(array $conditions = [])
    {
        $optionQuery = $this->tenantOption;

        if (!empty($conditions)) {
            foreach ($conditions as $column => $value) {
                $optionQuery = $optionQuery->where($column, $value);
            }
        }
        if (is_null($optionQuery->first())) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.ERROR_TENANT_OPTION_NOT_FOUND'));
        }
        return $optionQuery->first();
    }

    /**
    * Get a count of slider.
    *
    * @return int
    */
    public function getAllSliderCount(): int
    {
        $count = $this->tenantOption->where('option_name', config('constants.TENANT_OPTION_SLIDER'))->count();
    }

    /**
     * Create new option
     *
     * @param array $option
     * @return App\Models\TenantOption
     */
    public function store(array $option): TenantOption
    {
        return $this->tenantOption->create($option);
    }

    /**
     * Select by option name
     *
     * @param String $data
     * @return Illuminate\Support\Collection
     */
    public function getOptionValue(string $data): Collection
    {
        return $this->tenantOption->whereOption_name($data)->get();
    }
}
