<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\LatLongStruct;

class LatLong
{
    private readonly LatLongStruct $latLongStruct;

    public function __construct(string $latitude, string $longitude)
    {
        $this->latLongStruct = new LatLongStruct(latitude: $latitude, longitude: $longitude);
    }

    public function getLatitude() : string
    {
        return $this->latLongStruct->latitude;
    }

    public function getLongitude() : string
    {
        return $this->latLongStruct->longitude;
    }
}