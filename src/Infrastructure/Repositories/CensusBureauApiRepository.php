<?php

namespace Geocoding\Infrastructure\Repositories;

use Geocoding\Domain\Address;
use Geocoding\Domain\AddressDataRepositoryInterface;
use Geocoding\Infrastructure\Config\GeocodingConfig;

/**
 * This class is responsible for hitting the Census Bureau api and returning
 * the json response of the data we will use
 */

//vendor/bin/phpunit tests/Infrastructure/Repositories/CensusBureauApiRepositoryTest.php
class CensusBureauApiRepository implements AddressDataRepositoryInterface
{
    public GeocodingConfig $geocodingConfig;

    public function __construct(GeocodingConfig $geocodingConfig)
    {
        $this->geocodingConfig = $geocodingConfig;
    }

    public function generateUrl(Address $address) : string
    {
        $geocodeUrl = $this->geocodingConfig->censusBureauUrl;
        $addressGetParam = '?'. $this->geocodingConfig->censusBureauAddressGetParam. '=';
        $encodedAddress = $address->getUrlEncodedFullAddress() . '&';
        $benchMark = 'benchmark='. $this->geocodingConfig->censusBureauBenchMarkParam . '&';
        $format = 'format='. $this->geocodingConfig->censusBureauBenchMarkFormat;

        return $geocodeUrl. $addressGetParam. $encodedAddress. $benchMark. $format;
    }

    /**
     * Hits the geolocation api with an address and returns json response
     * of the request
     *
     * @return string - json string
     */
    public function fetchAddressCoordinates(Address $address) : array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->generateUrl($address));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $serverResponse = curl_exec($ch);

        curl_close($ch);

        return json_decode($serverResponse, true);
    }
}