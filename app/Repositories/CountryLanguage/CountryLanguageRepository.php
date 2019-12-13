<?php
namespace App\Repositories\CountryLanguage;

use App\Repositories\CountryLanguage\CountryLanguageInterface;
use App\Models\CountryLanguage;
use Illuminate\Support\Collection;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

class CountryLanguageRepository implements CountryLanguageInterface
{
    /**
     * @var App\Models\CountryLanguage
     */
    public $countryLanguage;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\CountryLanguage $countryLanguage
     * @return void
     */
    public function __construct(
        CountryLanguage $countryLanguage,
        LanguageHelper $languageHelper
    ) {
        $this->countryLanguage = $countryLanguage;
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
            
            $this->countryLanguage->create($data);
        }
    }
}
