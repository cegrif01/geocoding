<?php

namespace Geocoding\Domain\DataStructures;

class AddressStruct
{
    public readonly string $country;
    public readonly string $city;
    public readonly string $state;
    public readonly string $street;
    public readonly string $zip;

    public function __construct(string $country,
                                string $city,
                                string $state,
                                string $street,
                                string $zip)
    {
        $this->country = $country;
        $this->city = $city;
        $this->state = $state;
        $this->street = $street;
        $this->zip = $zip;
    }
}