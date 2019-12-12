<?php
namespace App\Repositories\CityTranslation;

use App\Repositories\CityTranslation\CityTranslationInterface;
use App\Models\CityTranslation;
use Illuminate\Support\Collection;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

class CityTranslationRepository implements CityTranslationInterface
{
    /**
     * @var App\Models\CityTranslation
     */
    public $cityTranslation;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\CityTranslation $cityTranslation
     * @return void
     */
    public function __construct(
        CityTranslation $cityTranslation,
        LanguageHelper $languageHelper
    )
    {
        $this->cityTranslation = $cityTranslation;
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
            
            $this->cityTranslation->create($data);
        }
    }
}
