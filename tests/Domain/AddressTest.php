<?php

namespace Tests\Domain;

use Geocoding\Domain\Address;
use Geocoding\Domain\DataStructures\AddressStruct;
use PHPUnit\Framework\TestCase;

//vendor/bin/phpunit tests/Domain/AddressTest.php
class AddressTest extends TestCase
{

    public function test_full_address()
    {
        $addressStruct = new AddressStruct(
            country: 'USA',
            city: 'Cincinnati',
            state: 'OH',
            street: '100 Joe Nuxhall Way',
            zip: '45202'
        );

        $address = new Address($addressStruct);

        $this->assertEquals($address->getFullAddress(), "100 Joe Nuxhall Way, Cincinnati, OH 45202");
    }

}