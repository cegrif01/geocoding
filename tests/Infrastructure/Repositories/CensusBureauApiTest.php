<?php

namespace Tests\Infrastructure\GeocodingApis;

use Geocoding\Infrastructure\Config\GeocodingConfig;
use Geocoding\Infrastructure\Repositories\CensusBureauApi;
use PHPUnit\Framework\TestCase;

//vendor/bin/phpunit tests/Infrastructure/Repositories/CensusBureauApiTest.php
class CensusBureauApiTest extends TestCase
{

    public function test_encodes_address_correctly()
    {
        $censusBureauApi = new CensusBureauApi(GeocodingConfig::make());

        $redsStadium = '100 Joe Nuxhall Way, Cincinnati, OH 45202';
        $encodedAddress = $censusBureauApi->encodeAddress($redsStadium);

        $this->assertEquals(
            expected: $encodedAddress,
            actual: '100%20Joe%20Nuxhall%20Way%2C%20Cincinnati%2C%20OH%2045202'
        );

    }

    public function test_generates_correct_url_from_config()
    {
        $censusBureauApi = new CensusBureauApi(GeocodingConfig::make());

        $redsStadium = '100 Joe Nuxhall Way, Cincinnati, OH 45202';
        $generatedUrl = $censusBureauApi->generateUrl($redsStadium);

        $this->assertEquals(
            expected: $generatedUrl,
            actual: 'https://geocoding.geo.census.gov/geocoder/locations/onelineaddress?address=100%20Joe%20Nuxhall%20Way%2C%20Cincinnati%2C%20OH%2045202&benchmark=4&format=json'
        );

    }

//    public function test_census_bureau_api_returns_correct_json_response()
//    {
//        $censusBureauApi = new CensusBureauApi(GeocodingConfig::make());
//
//        dd($censusBureauApi);
//
//    }
}