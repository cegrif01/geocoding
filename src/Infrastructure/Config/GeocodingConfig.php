<?php

namespace Geocoding\Config;

class GeocodingConfig
{
    public readonly string $censusBureauUrl;

    public readonly string $censusBureauAddressGetParam;

    public readonly string $censusBureauBenchMarkParam;

    public readonly string $censusBureauBenchMarkFormat;

    private function __construct(string $censusBureauUrl,
                                 string $censusBureauAddressGetParam,
                                 string $censusBureauBenchMarkParam,
                                 string $censusBureauBenchMarkFormat)
    {
        $this->censusBureauUrl = $censusBureauUrl;
        $this->censusBureauAddressGetParam = $censusBureauAddressGetParam;
        $this->censusBureauBenchMarkParam = $censusBureauBenchMarkParam;
        $this->censusBureauBenchMarkFormat = $censusBureauBenchMarkFormat;
    }

    public static function make(string $censusBureauUrl,
                                string $censusBureauAddressGetParam,
                                string $censusBureauBenchMarkParam,
                                string $censusBureauBenchMarkFormat)
    {
        return new static($censusBureauUrl,
                          $censusBureauAddressGetParam,
                          $censusBureauBenchMarkParam,
                          $censusBureauBenchMarkFormat);
    }


}