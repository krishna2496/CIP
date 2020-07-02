<?php

namespace App\Repositories\Currency;
use App\Repositories\Currency\Currency;

class CurrencyRepository
{
    /**
     * Create a new Language repository instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get listing of all currency
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
