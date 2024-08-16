<?php

namespace Geocoding\Infrastructure\Repositories;

use Geocoding\Domain\Address;
use Geocoding\Domain\AddressDataRepositoryInterface;
use Geocoding\Domain\LatLong;
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

    public function generateUrlFromAddress(Address $address) : string
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
     * todo break this function up
     *
     * @return array
     */
    public function fetchAddressCoordinates(Address $address) : LatLong
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->generateUrlFromAddress($address));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $serverResponse = curl_exec($ch);

        curl_close($ch);

        $responseData = json_decode($serverResponse, true);

        $coordinatesArray = $responseData['result']['addressMatches'][0]['coordinates'];

        $latitude = $coordinatesArray['x'];
        $longitude = $coordinatesArray['y'];

        return new LatLong(latitude: $latitude, longitude: $longitude);
    }
}