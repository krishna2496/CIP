<?php
namespace App\Repositories\CountryTranslation;

use App\Repositories\CountryTranslation\CountryTranslationInterface;
use App\Models\CountryTranslation;
use Illuminate\Support\Collection;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

class CountryTranslationRepository implements CountryTranslationInterface
{
    /**
     * @var App\Models\CountryTranslation
     */
    public $countryTranslation;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\CountryTranslation $countryTranslation
     * @return void
     */
    public function __construct(
        CountryTranslation $countryTranslation,
        LanguageHelper $languageHelper
    )
    {
        $this->countryTranslation = $countryTranslation;
        $this->languageHelper = $languageHelper;
    }
    
    /**
    * Get a listing of resource.
    *
    * @param array $data
    * @return void
    */
    public function store(Collection $languages, array $countryArray)
    {
        
        
        foreach ($countryArray['translations'] as $key => $country) {
            $data = [];
            $languageId = $languages->where('code', $country['lang'])->first()->language_id;
            
            $data['country_id'] = $countryArray['country_id'];
            $data['language_id'] = $languageId;
            $data['name'] = $country['name'];
            
            $this->countryTranslation->create($data);
        }
    }
}
