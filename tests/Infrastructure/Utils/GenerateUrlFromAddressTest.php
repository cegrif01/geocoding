<?php

namespace Tests\Infrastructure\Utils;

use Geocoding\Domain\Address;
use Geocoding\Infrastructure\Config\GeocodingConfig;
use Geocoding\Infrastructure\Utils\GenerateUrlFromAddress;
use PHPUnit\Framework\TestCase;

//vendor/bin/phpunit tests/Infrastructure/Utils/GenerateUrlFromAddressTest.php
class GenerateUrlFromAddressTest extends TestCase
{

    public function test_can_generate_url_from_an_address_domain()
    {
        $redsStadiumAddress = new Address(country: 'USA',
                                          city: 'Cincinnati',
                                          state: 'OH',
                                          street: '100 Joe Nuxhall Way',
                                          zip: '45202');

        $generatedUrlFromAddress = new GenerateUrlFromAddress(GeocodingConfig::make());

        $generatedUrl = $generatedUrlFromAddress->getUrl($redsStadiumAddress);

        $this->assertEquals(
            expected: $generatedUrl,
            actual: 'https://geocoding.geo.census.gov/geocoder/locations/onelineaddress?address=100%20Joe%20Nuxhall%20Way%2C%20Cincinnati%2C%20OH%2045202&benchmark=4&format=json'
        );
    }
}