<?php

namespace Geocoding\Infrastructure\Repositories;

use Geocoding\Domain\Address;
use Geocoding\Domain\AddressDataRepositoryInterface;
use Geocoding\Domain\LatLong;
use Geocoding\Infrastructure\Utils\GenerateUrlFromAddress;

/**
 * This class is responsible for hitting the Census Bureau api and returning
 * the json response of the data we will use
 */

//vendor/bin/phpunit tests/Infrastructure/Repositories/CensusBureauApiRepositoryTest.php
class CensusBureauApiRepository implements AddressDataRepositoryInterface
{

    public GenerateUrlFromAddress $generateUrlFromAddress;

    public function __construct(GenerateUrlFromAddress $generateUrlFromAddress)
    {
        $this->generateUrlFromAddress = $generateUrlFromAddress;
    }

    /**
     * Hits the geolocation api with an address and returns json response
     * of the request
     */
    public function fetchAddressCoordinates(Address $address) : LatLong
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->generateUrlFromAddress->getUrl($address));

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