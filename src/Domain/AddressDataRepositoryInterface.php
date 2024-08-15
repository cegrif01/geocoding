<?php

namespace Geocoding\Domain;

interface AddressDataRepositoryInterface
{

    public function fetchAddressCoordinates(Address $address) : array;
}