<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\LatLongStruct;

class LatLong
{
    private readonly LatLongStruct $latLongStruct;

    public function __construct(LatLongStruct $latLongStruct)
    {
        $this->latLongStruct = $latLongStruct;
    }

}