<?php

namespace Tests\Infrastructure\Repositories;

use Geocoding\Domain\Address;
use Geocoding\Domain\LatLong;
use Geocoding\Infrastructure\Config\GeocodingConfig;
use Geocoding\Infrastructure\Repositories\CensusBureauApiRepository;
use Geocoding\Infrastructure\Utils\GenerateUrlFromAddress;
use PHPUnit\Framework\TestCase;

//vendor/bin/phpunit tests/Infrastructure/Repositories/CensusBureauApiRepositoryTest.php
class CensusBureauApiRepositoryTest extends TestCase
{

    public function structureOfReturnFromCensusBureau() : array
    {
        return [
            "result" => [
                "input" => [
                    "address" => [
                        "address" => "100 Joe Nuxhall Way, Cincinnati, OH 45202",
                    ],
                    "benchmark" => [
                        "isDefault" => true,
                        "benchmarkDescription" => "Public Address Ranges - Current Benchmark",
                        "id" => "4",
                        "benchmarkName" => "Public_AR_Current"
                    ],
                ],
                "addressMatches" => [
                    [
                        "tigerLine" => [
                            "side" => "L",
                            "tigerLineId" => "647384196",
                        ],
                        "coordinates" => [
                            "x" => -84.50827551429869,
                            "y" => 39.09612212505558,
                        ],
                        "addressComponents" => [
                            "zip" => "45202",
                            "streetName" => "JOE NUXHALL",
                            "preType" => "",
                            "city" => "CINCINNATI",
                            "preDirection" => "",
                            "suffixDirection" => "",
                            "fromAddress" => "198",
                            "state" => "OH",
                            "suffixType" => "WAY",
                            "toAddress" => "100",
                            "suffixQualifier" => "",
                            "preQualifier" => "",
                        ],
                        "matchedAddress" => "100 JOE NUXHALL WAY, CINCINNATI, OH, 45202"
                    ]
                ]
            ]
        ];
    }

    public function test_census_bureau_api_returns_correct_json_response()
    {
        $censusBureauApi = new CensusBureauApiRepository(new GenerateUrlFromAddress(GeocodingConfig::make()));

        $redsStadiumAddress = new Address(country: 'USA',
                                          city: 'Cincinnati',
                                          state: 'OH',
                                          street: '100 Joe Nuxhall Way',
                                          zip: '45202');

        $latLong = $censusBureauApi->fetchAddressCoordinates($redsStadiumAddress);

        $this->assertEquals(new LatLong(latitude: '-84.508275514299', longitude: '39.096122125056'), $latLong);
    }
}