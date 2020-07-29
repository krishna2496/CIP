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
        return [
            new Currency('INR', '₹'),
            new Currency('EUR', '€'),
            new Currency('USD', '$'),
            new Currency('BRL', 'R$'),
            new Currency('ZWD', 'Z$'),
        ];
    }

    /**
     * Check request currency is available in currency list
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function isAvailableCurrency(string $currencyCode) : bool
    {
        $allCurrencyList = $this->findAll();
        $allCurrencyArray = [];
        $currencyMatch = 0;

        foreach ($allCurrencyList as $key => $value) {
            $getAvailableCurrencyCode = $value->code();
            if ($getAvailableCurrencyCode === $currencyCode) {
                $currencyMatch = 1;
            }
        }

        if ($currencyMatch === 1) {
            return true;
        }

        return false;
    }
}
