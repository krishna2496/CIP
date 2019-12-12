<?php
namespace App\Repositories\CityTranslation;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface CityTranslationInterface
{
    /**
    * Store resource.
    *
    * @return void
    */
    public function store(Collection $languages, array $country);
}
