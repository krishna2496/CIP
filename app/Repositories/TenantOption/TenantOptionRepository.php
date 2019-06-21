<?php
namespace App\Repositories\TenantOption;

use App\Repositories\TenantOption\TenantOptionInterface;
use Illuminate\Http\Request;
use Validator, PDOException, DB;
use App\Models\TenantOption;
use App\Helpers\{Helpers, ResponseHelper, DatabaseHelper};
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
    
    public function getAllSlider()
    {
        return $this->tenantOption->where('option_name', config('constants.TENANT_OPTION_SLIDER'))->get();
    }    

    /**
     * Store tenant slider data.
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
    public function storeSlider(array $data)
    {
        return $this->tenantOption->create($data);
    }

    /**
     * Store tenant option data
     *     
     * @return Illuminate\Support\Collection 
     */
    public function getOptions()
    {
        return $this->tenantOption->get(['option_name', 'option_value']);
    }

    public function getOptionWithCondition(array $conditions = [])
    {
        // dd($conditions);
        $optionQuery = $this->tenantOption;

        if (!empty($conditions)) {
            foreach ($conditions as $column => $value){
                $optionQuery = $optionQuery->where($column, $value);                
            }
        }        
        return $optionQuery->first();
    }
}
