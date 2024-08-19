<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\AddressStruct;
use Geocoding\Domain\Exceptions\InvalidAddressException;

//vendor/bin/phpunit tests/Domain/AddressTest.php
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
        $this->addressStruct = new AddressStruct($country, $city, $state, $street, $zip);

        /** @throws InvalidAddressException */
        $this->validate();
    }

    /**
     * @throws InvalidAddressException
     */
    private function validate() : void
    {

        if(!preg_match('/[a-zA-Z ]+/', $this->addressStruct->country)) {
            throw new InvalidAddressException($this->addressStruct->country. ' is not a valid country');
        }

        if(!preg_match('/[\d]+ [a-zA-Z ]+/',$this->addressStruct->street)) {
            throw new InvalidAddressException($this->addressStruct->street. ' is not a valid street');
        }

        if(!preg_match('/[a-zA-Z ]+/',$this->addressStruct->city)) {
            throw new InvalidAddressException($this->addressStruct->city. ' is not a valid city');
        }

        if(!preg_match('/[a-zA-Z ]+/',$this->addressStruct->state)) {
            throw new InvalidAddressException($this->addressStruct->state. ' is not a valid state');
        }

        if(!preg_match('/[\d]{5}(-[\d]{4})?/',$this->addressStruct->zip)) {
            throw new InvalidAddressException($this->addressStruct->zip.  ' is not a valid zip code');
        }
    }

    //example of an immutable setter
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