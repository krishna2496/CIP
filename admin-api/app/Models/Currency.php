<?php

namespace App\Models;

use App\Exceptions\InvalidCurrencyArgumentException;

final class Currency
{

    /**
     * Currency code
     *
     * @param string $code
     */
    private string $code;

    /**
     * Currency symbol
     *
     * @param string $symbol
     */
    private string $symbol;

    /**
     * Create a new currency instance.
     *
     * @param string $code
     * @param string $symbol
     * @return void
     */

    public function __construct($code,$symbol)
    {
        $this->setCode($code);
        $this->symbol = $symbol;
    }

    /**
     * Set currency code and check validation for currency code
     *
     * @param string $code
     * @return string|App\Exceptions\InvalidCurrencyArgumentException
     */
    private function setCode(string $code)
    {
        $pattern = '/^[A-Z]{3}$/m';
        $result = preg_match_all($pattern, $code, $matches);
        if (!empty($matches[0])) {
            return $this->code = $code;
        } else {
            throw new InvalidCurrencyArgumentException("Currency code {$code} is invalid.");
        }
    }

    /**
     * Get currency code
     * 
     * @return string $code
     */
    public function code(): string
    {
        return $this->code;
    }
    
    /**
     * Get currency symbol
     * 
     * @return string $symbol
     */
	public function symbol(): string
    {
        return $this->symbol;
    }
}
