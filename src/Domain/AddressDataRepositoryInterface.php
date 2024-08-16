<?php

namespace Geocoding\Domain;

use Geocoding\Domain\LatLong;

interface AddressDataRepositoryInterface
{

    public function fetchAddressCoordinates(Address $address) : LatLong;
}