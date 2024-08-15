<?php

namespace Geocoding\Infrastructure\Repositories;

use Geocoding\Infrastructure\Config\GeocodingConfig;

/**
 * This class is responsible for hitting the Census Bureau api and returning
 * the json response of the data we will use
 */

//vendor/bin/phpunit tests/Infrastructure/Repositories/CensusBureauApiTest.php
class CensusBureauApi
{
    public GeocodingConfig $geocodingConfig;

    public function __construct(GeocodingConfig $geocodingConfig)
    {
        $this->geocodingConfig = $geocodingConfig;
    }

    public function encodeAddress(string $fullAddress) : string
    {
        return rawurlencode($fullAddress);
    }

    public function generateUrl(string $fullAddress) : string
    {
        $geocodeUrl = $this->geocodingConfig->censusBureauUrl;
        $addressGetParam = '?'. $this->geocodingConfig->censusBureauAddressGetParam. '=';
        $encodedAddress = $this->encodeAddress($fullAddress) . '&';
        $benchMark = 'benchmark='. $this->geocodingConfig->censusBureauBenchMarkParam . '&';
        $format = 'format='. $this->geocodingConfig->censusBureauBenchMarkFormat;

        return $geocodeUrl. $addressGetParam. $encodedAddress. $benchMark. $format;
    }

    /**
     * Hits the geolocation api with an address and returns json response
     * of the request
     *
     * @return array
     */
    public function getLatitudeAndLongitude(string $fullAddress)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->generateUrl($fullAddress));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $serverResponse = curl_exec($ch);

        curl_close($ch);

        return $serverResponse;




    }


}