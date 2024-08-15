<?php

namespace Geocoding\Actions;

use Geocoding\Domain\Address;
use Geocoding\Domain\AddressDataRepositoryInterface;
use Geocoding\Domain\DataStructures\LatLongStruct;
use Geocoding\Domain\LatLong;
use Geocoding\Infrastructure\Repositories\CensusBureauApiRepository;

//vendor/bin/phpunit tests/Actions/ConvertAddressIntoLatAndLongActionTest.php
class ConvertAddressIntoLatAndLongAction
{
    /** @var CensusBureauApiRepository  */
    private AddressDataRepositoryInterface $addressDataRepository;

    public function __construct(AddressDataRepositoryInterface $addressDataRepository)
    {
        $this->addressDataRepository = $addressDataRepository;
    }

    public function __invoke(Address $address) : LatLong
    {

        $apiResponseData = $this->addressDataRepository->fetchAddressCoordinates($address);

        $coordinatesArray = $apiResponseData['result']['addressMatches'][0]['coordinates'];

        $latitude = $coordinatesArray['x'];
        $longitude = $coordinatesArray['y'];

        return new LatLong(new LatLongStruct(latitude: $latitude, longitude: $longitude));
    }

}