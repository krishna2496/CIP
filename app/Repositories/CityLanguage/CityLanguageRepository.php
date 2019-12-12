<?php
namespace App\Repositories\CityLanguage;

use App\Repositories\CityLanguage\CityLanguageInterface;
use App\Models\CityLanguage;
use Illuminate\Support\Collection;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

class CityLanguageRepository implements CityLanguageInterface
{
    /**
     * @var App\Models\CityLanguage
     */
    public $cityLanguage;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\CityLanguage $cityLanguage
     * @return void
     */
    public function __construct(
        CityLanguage $cityLanguage,
        LanguageHelper $languageHelper
    )
    {
        $this->cityLanguage = $cityLanguage;
        $this->languageHelper = $languageHelper;
    }
    
    /**
    * Get a listing of resource.
    *
    * @param array $data
    * @return void
    */
    public function store(Collection $languages, array $cityArray)
    {
        foreach ($cityArray['translations'] as $key => $city) {
            $data = [];
            $languageId = $languages->where('code', $city['lang'])->first()->language_id;
            
            $data['city_id'] = $cityArray['city_id'];
            $data['language_id'] = $languageId;
            $data['name'] = $city['name'];
            
            $this->cityLanguage->create($data);
        }
    }
}
