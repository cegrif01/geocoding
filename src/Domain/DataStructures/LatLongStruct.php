<?php

namespace Geocoding\Domain\DataStructures;

class LatLongStruct
{

    public readonly string $latitude;

    public readonly string $longitude;

    public function __construct(string $latitude, string $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}