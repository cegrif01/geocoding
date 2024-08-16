<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\AddressStruct;

class Address
{
    public readonly AddressStruct $addressStruct;

    public function __construct(string $country,
                                string $city,
                                string $state,
                                string $street,
                                string $zip)
    {
        //validate the input
        $this->addressStruct = new AddressStruct($country, $city, $state, $street, $zip);
    }

    public function updateCity(string $city) : Address
    {
         return new Address($this->addressStruct->country,
                            $city,
                            $this->addressStruct->state,
                            $this->addressStruct->street,
                            $this->addressStruct->zip,
        );
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