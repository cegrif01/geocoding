<?php

namespace Tests\Actions;

use Geocoding\Domain\DataStructures\AddressStruct;
use Geocoding\Domain\DataStructures\LatLongStruct;
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
        $addressStruct = new AddressStruct(
            country: 'USA',
            city: 'Cincinnati',
            state: 'OH',
            street: '100 Joe Nuxhall Way',
            zip: '45202'
        );

        $redsStadiumAddress = new Address($addressStruct);

        /** @var ConvertAddressIntoLatAndLongAction $addressConverter */
        $addressConverterAction = (new ConvertAddressIntoLatAndLongAction(new CensusBureauApiRepository(GeocodingConfig::make())));

        $this->assertEquals(
            $addressConverterAction($redsStadiumAddress),
            new LatLong(new LatLongStruct(latitude: '-84.508275514299', longitude: '39.096122125056'))
        );

    }

}