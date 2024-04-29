<?php

namespace App\Helpers\Remote;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Get the currecy exchange rates
 *
 * @package App\Helpers
 * @author  Jason Witt
 * @version 1.0.0
 */
class ExchangeRates
{

    /**
     * Rates Cache Key
     *
     * @var string
     */
    protected static $rates_cache_key = 'currenct_convert_rates';

    /**
     * Rate Cache Time
     *
     * 30 Days
     *
     * @var string
     *
     * @author Jason Witt
     * @since  1.0.0
     */
    protected static $rates_cache_time = '2592000';

    /**
     * API URL
     *
     * @var string
     */
    protected static $api_url = 'http://data.fixer.io/api/';

    /**
     * API Endpoint
     *
     * @var string
     */
    protected static $api_endpoint = 'latest';

    /**
     * API Key
     *
     * @var string
     */
    protected static $api_key = 'FIXER_KEY';

    /**
     * Currencies
     *
     * @var array
     */
    protected static $currencies = [
        'AED',
        'AFN',
        'ALL',
        'AMD',
        'ANG',
        'AOA',
        'ARS',
        'AUD',
        'AWG',
        'AZN',
        'BAM',
        'BBD',
        'BDT',
        'BGN',
        'BHD',
        'BIF',
        'BMD',
        'BND',
        'BOB',
        'BRL',
        'BSD',
        'BTC',
        'BTN',
        'BWP',
        'BYR',
        'BYN',
        'BZD',
        'CAD',
        'CDF',
        'CHF',
        'CLP',
        'CNY',
        'COP',
        'CRC',
        'CUC',
        'CUP',
        'CVE',
        'CZK',
        'DJF',
        'DKK',
        'DOP',
        'DZD',
        'EGP',
        'ERN',
        'ETB',
        'EUR',
        'FJD',
        'FKP',
        'GBP',
        'GEL',
        'GGP',
        'GHS',
        'GIP',
        'GMD',
        'GNF',
        'GTQ',
        'GYD',
        'HKD',
        'HNL',
        'HRK',
        'HTG',
        'HUF',
        'IDR',
        'ILS',
        'IMP',
        'INR',
        'IQD',
        'IRR',
        'IRT',
        'ISK',
        'JEP',
        'JMD',
        'JOD',
        'JPY',
        'KES',
        'KGS',
        'KHR',
        'KMF',
        'KPW',
        'KRW',
        'KWD',
        'KYD',
        'KZT',
        'LAK',
        'LBP',
        'LKR',
        'LRD',
        'LSL',
        'LYD',
        'MAD',
        'MDL',
        'MGA',
        'MKD',
        'MMK',
        'MNT',
        'MOP',
        'MRU',
        'MUR',
        'MVR',
        'MWK',
        'MXN',
        'MYR',
        'MZN',
        'NAD',
        'NGN',
        'NIO',
        'NOK',
        'NPR',
        'NZD',
        'OMR',
        'PAB',
        'PEN',
        'PGK',
        'PHP',
        'PKR',
        'PLN',
        'PRB',
        'PYG',
        'QAR',
        'RMB',
        'RON',
        'RSD',
        'RUB',
        'RWF',
        'SAR',
        'SBD',
        'SCR',
        'SDG',
        'SEK',
        'SGD',
        'SHP',
        'SLL',
        'SOS',
        'SRD',
        'SSP',
        'STN',
        'SYP',
        'SZL',
        'THB',
        'TJS',
        'TMT',
        'TND',
        'TOP',
        'TRY',
        'TTD',
        'TWD',
        'TZS',
        'UAH',
        'UGX',
        'USD',
        'UYU',
        'UZS',
        'VEF',
        'VES',
        'VND',
        'VUV',
        'WST',
        'XAF',
        'XCD',
        'XOF',
        'XPF',
        'YER',
        'ZAR',
        'ZMW',
    ];


    /**
     * Get the current exchange rates
     *
     * @since  1.0.0
     * @return object
     *
     * @author Jason Witt
     */
    public static function rates()
    {
        return (Cache::get(self::$rates_cache_key) ?? self::get_rates_from_api(self::$currencies, 'USD'));

    }//end rates()


    /**
     * Get Rates from API
     *
     * @param array  $currencies The array of currency symbols to convert
     * @param string $base       The base currency to convert against.
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return object
     */
    public static function get_rates_from_api(array $currencies, string $base)
    {
        $symbols  = implode(',', $currencies);
        $response = curl_init(self::$api_url.self::$api_endpoint.'?access_key='.env(self::$api_key, 'Laravel').'&base='.$base.'&symbols='.$symbols);
        curl_setopt($response, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($response);
        $data = json_decode($json);

        curl_close($response);

        if (!empty($data->success)) {
            if (!empty($data->rates)) {
                Cache::put(self::$rates_cache_key, $data->rates, self::$rates_cache_time);
                return $data->rates;
            }
        } else {
            echo $data->error->info;
            die;
        }

    }//end get_rates_from_api()


    /**
     * Convert
     *
     * @param float  $amount   The amount to convert.
     * @param string $currency The currency
     *
     * @return float
     *
     * @since 1.0.0
     *
     * @author Jason Witt
     */
    public static function convert(float $amount, string $currency): float
    {
        if ($amount === 0.0) {
            return $amount;
        }

        if ($currency === 'USD') {
            return $amount;
        }

        $rates = self::rates();

        if (empty($rates)) {
            return $amount;
        }

        $foreignRate = ($rates->{$currency} ?? false);

        if (! $foreignRate) {
            return $amount;
        }

        $amount = ($amount / floatval($foreignRate));

        return round($amount, 2);

    }//end convert()


}//end class
