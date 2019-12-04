<?php
namespace App\Repositories\CountryTranslation;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface CountryTranslationInterface
{
    /**
    * Store resource.
    *
    * @return void
    */
    public function store(Collection $languages, array $country);
}
