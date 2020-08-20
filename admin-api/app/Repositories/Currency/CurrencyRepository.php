<?php

namespace App\Repositories\Currency;

use App\Models\Currency;

class CurrencyRepository
{
    /**
     * Create available currency instance
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

    /**
     * Check request currency is available in currency list
     *
     * @param string $currencyCode
     * @return array
     */
    public function isAvailableCurrency(string $currencyCode) : array
    {
        $allCurrencyList = $this->findAll();
        $allCurrencyArray = [];
        $currencyMatch = 0;

        foreach ($allCurrencyList as $key => $value) {
            $getAvailableCurrencyCode = $value->code();
            //check code is in proper format ISO-4217 or not
            $pattern = '/^[A-Z]{3}$/m';
            $result = preg_match_all($pattern, $getAvailableCurrencyCode, $matches);
            if (empty($matches[0])) {
                return [
                    false,
                    'systemCurrencyInvalid' => true,
                    'systemCurrency' => $getAvailableCurrencyCode
                ];
            }

            //check system code and request code are same
            if ($getAvailableCurrencyCode === $currencyCode) {                
                return [
                    true,
                    $getAvailableCurrencyCode
                ];
            }
        }
        
        return [
            false,
            'systemCurrencyInvalid' => false,
            'systemCurrency' => $currencyCode
        ];
    }
}
