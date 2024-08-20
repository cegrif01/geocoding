<?php

namespace Tests\Domain;

use Geocoding\Domain\Exceptions\InvalidLatitudeAndLongitudeException;
use Geocoding\Domain\LatLong;
use PHPUnit\Framework\TestCase;

//vendor/bin/phpunit tests/Domain/LongLatTest.php
class LatLongTest extends TestCase
{

    public function test_invalid_lat_n_long()
    {
        $this->expectException(InvalidLatitudeAndLongitudeException::class);

        $latLong = new LatLong('abc', 'bcd@');
    }

    public function test_valid_lat_n_long()
    {
        $latLong = new LatLong('-84.508275514299', '39.096122125056');

        $this->assertEquals('-84.508275514299', $latLong->getLatitude());
        $this->assertEquals('39.096122125056', $latLong->getLongitude());
    }
}