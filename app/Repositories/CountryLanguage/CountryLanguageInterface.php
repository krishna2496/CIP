<?php
namespace App\Repositories\CountryLanguage;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface CountryLanguageInterface
{
    /**
    * Store resource.
    *
    * @return void
    */
    public function store(Collection $languages, array $country);
}
