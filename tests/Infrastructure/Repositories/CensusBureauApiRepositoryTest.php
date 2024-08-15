<?php

namespace Tests\Infrastructure\Repositories;

use Geocoding\Domain\Address;
use Geocoding\Domain\DataStructures\AddressStruct;
use Geocoding\Infrastructure\Config\GeocodingConfig;
use Geocoding\Infrastructure\Repositories\CensusBureauApiRepository;
use PHPUnit\Framework\TestCase;

//vendor/bin/phpunit tests/Infrastructure/Repositories/CensusBureauApiRepositoryTest.php
class CensusBureauApiRepositoryTest extends TestCase
{

    public function test_generates_correct_url_from_config()
    {
        $addressStruct = new AddressStruct(
            country: 'USA',
            city: 'Cincinnati',
            state: 'OH',
            street: '100 Joe Nuxhall Way',
            zip: '45202'
        );

        $redsStadiumAddress = new Address($addressStruct);

        $censusBureauApi = new CensusBureauApiRepository(GeocodingConfig::make());

        $generatedUrl = $censusBureauApi->generateUrlFromAddress($redsStadiumAddress);

        $this->assertEquals(
            expected: $generatedUrl,
            actual: 'https://geocoding.geo.census.gov/geocoder/locations/onelineaddress?address=100%20Joe%20Nuxhall%20Way%2C%20Cincinnati%2C%20OH%2045202&benchmark=4&format=json'
        );
    }

    public function test_census_bureau_api_returns_correct_json_response()
    {
        $censusBureauApi = new CensusBureauApiRepository(GeocodingConfig::make());

        $addressStruct = new AddressStruct(
            country: 'USA',
            city: 'Cincinnati',
            state: 'OH',
            street: '100 Joe Nuxhall Way',
            zip: '45202'
        );

        $redsStadiumAddress = new Address($addressStruct);

        $response = $censusBureauApi->fetchAddressCoordinates($redsStadiumAddress);

        $this->assertEquals(
                [
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
                ],
            $response);
    }
}