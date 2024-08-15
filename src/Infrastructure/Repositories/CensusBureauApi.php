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