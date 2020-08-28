<?php

namespace App\Repositories\Currency;
use App\Repositories\Currency\CurrencyInterface;

use App\Models\Currency;

class CurrencyRepository implements CurrencyInterface
{
    /**
     * Create currency instance
     */
    public function __construct()
    {
    }

    /**
     * Get list of all currency
     *
     * @return array
     */
    public function findAll()
    {
        $currency = config('constants.currency');
        $currencyArray = [];
        foreach($currency as $code => $symbol) {
            array_push($currencyArray, new Currency($code, $symbol));
        }
        return $currencyArray;
    }
}
