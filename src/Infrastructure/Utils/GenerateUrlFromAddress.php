<?php

namespace Geocoding\Infrastructure\Utils;

use Geocoding\Domain\Address;
use Geocoding\Infrastructure\Config\GeocodingConfig;

//vendor/bin/phpunit tests/Infrastructure/Utils/GenerateUrlFromAddressTest.php
class GenerateUrlFromAddress
{
    public GeocodingConfig $geocodingConfig;

    public function __construct(GeocodingConfig $geocodingConfig)
    {
        $this->geocodingConfig = $geocodingConfig;
    }

    public function getUrl(Address $address) : string
    {
        $geocodeUrl = $this->geocodingConfig->censusBureauUrl;
        $addressGetParam = '?'. $this->geocodingConfig->censusBureauAddressGetParam. '=';
        $encodedAddress = $address->getUrlEncodedFullAddress() . '&';
        $benchMark = 'benchmark='. $this->geocodingConfig->censusBureauBenchMarkParam . '&';
        $format = 'format='. $this->geocodingConfig->censusBureauBenchMarkFormat;

        return $geocodeUrl. $addressGetParam. $encodedAddress. $benchMark. $format;
    }
}