<?php

namespace Tests\Actions;

use Geocoding\Domain\LatLong;
use Geocoding\Infrastructure\Config\GeocodingConfig;
use Geocoding\Infrastructure\Repositories\CensusBureauApiRepository;
use PHPUnit\Framework\TestCase;
use Geocoding\Domain\Address;
use Geocoding\Actions\ConvertAddressIntoLatAndLongAction;


//vendor/bin/phpunit tests/Actions/ConvertAddressIntoLatAndLongActionTest.php
class ConvertAddressIntoLatAndLongActionTest extends TestCase
{

    public function test_can_convert_address_into_lat_and_long()
    {
        $redsStadiumAddress = new Address(country: 'USA',
                                          city: 'Cincinnati',
                                          state: 'OH',
                                          street: '100 Joe Nuxhall Way',
                                          zip: '45202');

        /** @var ConvertAddressIntoLatAndLongAction $addressConverter */
        $addressConverterAction = (new ConvertAddressIntoLatAndLongAction(new CensusBureauApiRepository(GeocodingConfig::make())));

        $this->assertEquals(
            $addressConverterAction($redsStadiumAddress),
            new LatLong(latitude: '-84.508275514299', longitude: '39.096122125056')
        );

    }

}