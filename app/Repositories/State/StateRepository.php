<?php
namespace App\Repositories\State;

use Illuminate\Http\Request;
use App\Repositories\State\StateInterface;
use App\Models\Country;
use Illuminate\Support\Collection;
use App\Helpers\LanguageHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\State;
use App\Models\StateLanguage;

class StateRepository implements StateInterface
{
    /**
     * @var App\Models\State
     */
    public $state;

    /**
     * @var App\Models\StateLanguage
     */
    public $stateLanguage;

    /**
     * @var App\Models\Country
     */
    public $country;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\State $state
     * @param App\Models\Country $country
     * @param App\Models\StateLanguage $stateLanguage
     * @param App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(
        State $state,
        Country $country,
        StateLanguage $stateLanguage,
        LanguageHelper $languageHelper
    ) {
        $this->state = $state;
        $this->country = $country;
        $this->stateLanguage = $stateLanguage;
        $this->languageHelper = $languageHelper;
    }
    
    /**
    * Get listing of all state.
    *
    * @param Illuminate\Http\Request $request
    * @return Illuminate\Pagination\LengthAwarePaginator
    */
    public function stateLists(Request $request): LengthAwarePaginator
    {
        $states = $this->state->with('languages')->paginate($request->perPage);

        $languages = $this->languageHelper->getLanguages();
        foreach ($states as $key => $value) {
            foreach ($value->languages as $languageValue) {
                $languageData = $languages->where('language_id', $languageValue->language_id)->first();
                $languageValue->language_code = $languageData->code;
            }
        }
        return $states;
    }

    /**
     * Store state data
     *
     * @param string $countryId
     * @return State
     */
    public function store(string $countryId): State
    {
        return $this->state->create(['country_id' => $countryId]);
    }

    /**
     * Store state language data
     *
     * @param array $stateData
     * @return void
     */
    public function storeStateLanguage(array $stateData)
    {
        $languages = $this->languageHelper->getLanguages();
        
        foreach ($stateData['translations'] as $key => $state) {
            $data = [];
            $languageId = $languages->where('code', $state['lang'])->first()->language_id;
            
            $data['state_id'] = $stateData['state_id'];
            $data['language_id'] = $languageId;
            $data['name'] = $state['name'];
            
            $this->stateLanguage->create($data);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->state->deleteState($id);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  int $id
    * @return App\Models\State
    */
    public function update(Request $request, int $id): State
    {
        // Set data for update record
        $stateDetail = array();
        if (isset($request['country_id'])) {
            $stateDetail['country_id'] = $request['country_id'];
        }
        
        // Update state
        $stateData = $this->state->findOrFail($id);
        $stateData->update($stateDetail);
        
        $languages = $this->languageHelper->getLanguages();
                 
        if (isset($request['translations'])) {
            foreach ($request['translations'] as $value) {
                $language = $languages->where('code', $value['lang'])->first();
                $stateLanguageData = [
                    'state_id' => $id,
                    'name' => $value['name'],
                    'language_id' => $language->language_id
                ];

                $this->stateLanguage->createOrUpdateStateLanguage(['state_id' => $id,
                 'language_id' => $language->language_id], $stateLanguageData);
                unset($stateLanguageData);
            }
        }
        return $stateData;
    }

    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return App\Models\State
     */
    public function find(int $id): State
    {
        return $this->state->findOrFail($id);
    }

    /**
     * It will check is state belongs to any mission or not
     *
     * @param int $id
     * @return bool
     */
    public function hasMission(int $id): bool
    {
        return $this->state->whereHas('mission')->whereStateId($id)->count() ? true : false;
    }
    
    /**
     * It will check is state belongs to any user or not
     *
     * @param int $id
     * @return bool
     */
    public function hasUser(int $id): bool
    {
        return $this->state->whereHas('user')->whereStateId($id)->count() ? true : false;
    }

    /**
    * Get listing of all state by country wise with pagination.
    *
    * @param Illuminate\Http\Request $request
    * @param int $countryId
    * @return Illuminate\Pagination\LengthAwarePaginator
    */
    public function getStateList(Request $request, int $countryId) : LengthAwarePaginator
    {
        $this->country->findOrFail($countryId);
        $states = $this->state->with('languages')->where('country_id', $countryId)->paginate($request->perPage);

        $languages = $this->languageHelper->getLanguages();
        foreach ($states as $key => $value) {
            foreach ($value->languages as $languageValue) {
                $languageData = $languages->where('language_id', $languageValue->language_id)->first();
                $languageValue->language_code = $languageData->code;
            }
        }
        return $states;
    }

    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return App\Models\State
     */
    public function getStateDetails(int $id): State
    {
        return $this->state->with('languages')->findOrFail($id);
    }
}
