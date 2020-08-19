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
            new Currency('USD', '$'),
            new Currency('AED', 'د.إ'),
            new Currency('AFN', '؋'),
            new Currency('ALL', 'L'),
            new Currency('AMD', 'դր.'),
            new Currency('ANG', 'ƒ'),
            new Currency('AOA', 'Kz'),
            new Currency('ARS', '$'),
            new Currency('AUD', '$'),
            new Currency('AWG', 'ƒ'),
            new Currency('AZN', 'm'),
            new Currency('BAM', 'КМ'),
            new Currency('BBD', '$'),
            new Currency('BDT', '৳'),
            new Currency('BGN', 'лв'),
            new Currency('BIF', 'Fr'),
            new Currency('BMD', '$'),
            new Currency('BND', '$'),
            new Currency('BOB', 'Bs.'),
            new Currency('BRL', 'R$'),
            new Currency('BSD', '₹'),
            new Currency('BWP', 'P'),
            new Currency('BZD', '$'),
            new Currency('CAD', 'R$'),
            new Currency('CDF', 'Fr'),
            new Currency('CHF', 'Fr'),
            new Currency('CLP', '$'),
            new Currency('CNY', '¥'),
            new Currency('COP', '$'),
            new Currency('CRC', '₡'),
            new Currency('CVE', '$'),
            new Currency('CZK', 'Kč'),
            new Currency('DJF', 'Fr'),
            new Currency('DKK', 'kr'),
            new Currency('DOP', '$'),
            new Currency('DZD', 'د.ج'),
            new Currency('EGP', '€'),
            new Currency('ETB', 'ج.م'),
            new Currency('EUR', '€'),
            new Currency('FJD', '$'),
            new Currency('FKP', '£'),
            new Currency('GBP', '£'),
            new Currency('GEL', 'ლ'),
            new Currency('GIP', '£'),
            new Currency('GMD', 'D'),
            new Currency('GNF', 'Fr'),
            new Currency('GTQ', 'Q'),
            new Currency('GYD', '$'),
            new Currency('HKD', '$'),
            new Currency('HNL', 'L'),
            new Currency('HRK', 'kn'),
            new Currency('HTG', 'G'),
            new Currency('HUF', 'Ft'),
            new Currency('IDR', 'Rp'),
            new Currency('ILS', '₪'),
            new Currency('INR', '₹'),
            new Currency('ISK', 'kr'),
            new Currency('JMD', '$'),
            new Currency('JPY', '¥'),
            new Currency('KES', 'Sh'),
            new Currency('KGS', 'лв'),
            new Currency('KHR', '€'),
            new Currency('KMF', '៛'),
            new Currency('KRW', '₩'),
            new Currency('KYD', '$'),
            new Currency('KZT', '₸'),
            new Currency('LAK', '₭'),
            new Currency('LBP', 'ل.ل'),
            new Currency('LKR', 'Rs'),
            new Currency('LRD', '$'),
            new Currency('LSL', 'L'),
            new Currency('MAD', 'د.م.'),
            new Currency('MDL', 'L'),
            new Currency('MGA', 'Ar'),
            new Currency('MKD', 'ден'),
            new Currency('MMK', 'Ks'),
            new Currency('MNT', '₮'),
            new Currency('MOP', 'P'),
            new Currency('MRO', 'UM'),
            new Currency('MUR', '₨'),
            new Currency('MVR', '.ރ'),
            new Currency('MWK', 'MK'),
            new Currency('MXN', '$'),
            new Currency('MYR', 'RM'),
            new Currency('MZN', 'MT'),
            new Currency('NAD', '$'),
            new Currency('NGN', '₦'),
            new Currency('NIO', 'C$'),
            new Currency('NOK', 'kr'),
            new Currency('NPR', '₨'),
            new Currency('NZD', '$'),
            new Currency('PAB', 'B/.'),
            new Currency('PEN', 'S/.'),
            new Currency('PGK', 'K'),
            new Currency('PHP', '₱'),
            new Currency('PKR', '₨'),
            new Currency('PLN', 'zł'),
            new Currency('PYG', '₲'),
            new Currency('QAR', 'ر.ق'),
            new Currency('RON', 'L'),
            new Currency('RSD', 'дин.'),
            new Currency('RUB', 'руб.'),
            new Currency('RWF', 'Fr'),
            new Currency('SAR', 'ر.س'),
            new Currency('SBD', '$'),
            new Currency('SCR', '₨'),
            new Currency('SEK', 'kr'),
            new Currency('SGD', '$'),
            new Currency('SHP', '£'),
            new Currency('SLL', 'Le'),
            new Currency('SOS', 'Sh'),
            new Currency('SRD', '$'),
            new Currency('STD', 'Db'),
            new Currency('SZL', 'L'),
            new Currency('THB', '฿'),
            new Currency('TJS', 'ЅМ'),
            new Currency('TOP', 'T$'),
            new Currency('TRY', 'NULL'),
            new Currency('TTD', '$'),
            new Currency('TWD', '$'),
            new Currency('TZS', 'Sh'),
            new Currency('UAH', '₴'),
            new Currency('UGX', 'Sh'),
            new Currency('UYU', '$'),
            new Currency('UZS', 'лв'),
            new Currency('VND', '₫'),
            new Currency('VUV', 'Vt'),
            new Currency('WST', 'T'),
            new Currency('XAF', 'Fr'),
            new Currency('XCD', '$'),
            new Currency('XOF', 'Fr'),
            new Currency('XPF', 'Fr'),
            new Currency('YER', '﷼'),
            new Currency('ZAR', 'R'),
            new Currency('ZMW', 'ZK'),
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
                return true;
            }
        }
        
        return false;
    }
}
