<?php

namespace App\Repositories\Currency;

final class Currency
{

    // ISO-4217 currency code
    public string $code;
    public string $symbol;

    /**
     * Create a new Language repository instance.
     *
     * @param string $code
     * @param string $symbol
     * @return void
     */

    public function __construct($code, $symbol)
    {
        $this->code = $code;
        $this->symbol = $symbol;
    }

    /**
     * get Currency code and check valid or not
     *
     * @return string|boolean
     */
    public function getCode()
    {
        $pattern = '/^[A-Z]{3}$/m';
        $result = preg_match_all($pattern, $this->code, $matches);
        if (!empty($matches[0])) {
            return $this->code;
        } else {
            return false;
        }
    }
}
