<?php

namespace Tests\Infrastructure\GeocodingApis;

use Geocoding\Config\GeocodingConfig;
use Geocoding\Infrastructure\Repositories\CensusBureauApi;
use PHPUnit\Framework\TestCase;

//vendor/bin/phpunit tests/Infrastructure/Repositories/CensusBureauApiTest.php
class CensusBureauApiTest extends TestCase
{

    public function test_census_bureau_api_returns_correct_json_response()
    {
        $censusBureauApi = new CensusBureauApi(GeocodingConfig::make());

        var_dump($censusBureauApi);
        die;

    }
}