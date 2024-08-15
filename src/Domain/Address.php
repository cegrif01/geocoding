<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\AddressStruct;

class Address
{
    public readonly AddressStruct $addressStruct;

    public function __construct(AddressStruct $addressStruct)
    {
        $this->addressStruct = $addressStruct;
    }

    public function getFullAddress() : string
    {
        return $this->addressStruct->street . ', ' .
               $this->addressStruct->city . ', '.
               $this->addressStruct->state . ' '.
               $this->addressStruct->zip;
    }

    public function getUrlEncodedFullAddress() : string
    {
        return rawurlencode($this->getFullAddress());
    }
}