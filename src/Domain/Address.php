<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\AddressStruct;
use Geocoding\Domain\Exceptions\InvalidAddressException;

class Address
{
    public readonly AddressStruct $addressStruct;

    /**
     * @throws InvalidAddressException
     */
    public function __construct(string $country,
                                string $city,
                                string $state,
                                string $street,
                                string $zip)
    {
        /** @throws InvalidAddressException */
        $this->validate($country, $city, $state, $street, $zip);

        $this->addressStruct = new AddressStruct($country, $city, $state, $street, $zip);
    }

    /**
     * @throws InvalidAddressException
     */
    private function validate(string $country,
                              string $city,
                              string $state,
                              string $street,
                              string $zip) : void
    {

        if(!preg_match('/[a-zA-Z ]+/',$country)) {
            throw new InvalidAddressException($country. ' is not a valid country');
        }

        if(!preg_match('/[\d]+ [a-zA-Z ]+/',$street)) {
            throw new InvalidAddressException($street. ' is not a valid street');
        }

        if(!preg_match('/[a-zA-Z ]+/',$city)) {
            throw new InvalidAddressException($city. ' is not a valid city');
        }

        if(!preg_match('/[a-zA-Z ]+/',$state)) {
            throw new InvalidAddressException($state. ' is not a valid state');
        }

        if(!preg_match('/[\d]{5}(-[\d]{4})?/',$zip)) {
            throw new InvalidAddressException($zip.  ' is not a valid zip code');
        }
    }

    public function setCity(string $city) : Address
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