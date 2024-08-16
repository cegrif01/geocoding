<?php

namespace Geocoding\Actions;

use Geocoding\Domain\Address;
use Geocoding\Domain\AddressDataRepositoryInterface;
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
        return $this->addressDataRepository->fetchAddressCoordinates($address);
    }
}