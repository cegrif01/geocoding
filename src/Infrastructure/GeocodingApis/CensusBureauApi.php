<?php

namespace Geocoding\Infrastructure\GeocodingApis;

/**
 * This class is responsible for hitting the Census Bureau api and returning
 * the json response of the data we will use
 */

//vendor/bin/phpunit tests/Infrastructure/GeocodingApis/CensusBureauApiTest.php
class CensusBureauApi
{

    /**
     * Hits the geolocation api with an address and returns json response
     * of the request
     *
     * @return array
     */
    public function getLatitudeAndLongitude(string $fullAddress) : array
    {

    }


}