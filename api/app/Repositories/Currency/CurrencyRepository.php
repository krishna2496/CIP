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
        return [
            new Currency('INR', '₹'),
            new Currency('EUR', '€'),
            new Currency('USD', '$'),
            new Currency('BRL', 'R$'),
            new Currency('ZWD', 'Z$'),
        ];
    }
}
