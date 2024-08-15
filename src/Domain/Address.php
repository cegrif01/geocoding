<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\AddressStruct;

class Address
{
    private readonly AddressStruct $addressStruct;

    public function __construct(AddressStuct $addressStruct)
    {
        $this->addressStruct = $addressStruct;
    }

    public function getFullAddress() : string
    {

    }

    public function encodeAddress(string $fullAddress) : string
    {
        return rawurlencode($fullAddress);
    }
}