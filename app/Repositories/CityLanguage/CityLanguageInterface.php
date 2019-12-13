<?php
namespace App\Repositories\CityLanguage;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface CityLanguageInterface
{
    /**
    * Store resource.
    *
    * @return void
    */
    public function store(Collection $languages, array $country);
}
