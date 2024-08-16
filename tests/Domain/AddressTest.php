<?php

namespace Tests\Domain;

use Geocoding\Domain\Address;
use PHPUnit\Framework\TestCase;

//vendor/bin/phpunit tests/Domain/AddressTest.php
class AddressTest extends TestCase
{

    public function test_full_address_is_correct()
    {
        $address = new Address(country: 'USA',
            city: 'Cincinnati',
            state: 'OH',
            street: '100 Joe Nuxhall Way',
            zip: '45202');

        $this->assertEquals($address->getFullAddress(), "100 Joe Nuxhall Way, Cincinnati, OH 45202");
    }

    public function test_url_encodes_full_address()
    {
        $address = new Address(country: 'USA',
            city: 'Cincinnati',
            state: 'OH',
            street: '100 Joe Nuxhall Way',
            zip: '45202');

        $this->assertEquals($address->getUrlEncodedFullAddress(), '100%20Joe%20Nuxhall%20Way%2C%20Cincinnati%2C%20OH%2045202');
    }
}