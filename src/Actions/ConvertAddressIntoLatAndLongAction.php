<?php

namespace Geocoding\Actions;

use Geocoding\DataStructures\AddressStruct;
use Geocoding\DataStructures\LatLongStruct;
use Geocoding\Domain\AddressDataRepositoryInterface;
use Geocoding\Infrastructure\Repositories\CensusBureauApiRepository;

class ConvertAddressIntoLatAndLongAction
{
    /** @var CensusBureauApiRepository  */
    private AddressDataRepositoryInterface $addressDataRepository;

    public function __construct(AddressDataRepositoryInterface $addressDataRepository)
    {
        $this->addressDataRepository = $addressDataRepository;
    }

    public function __invoke()
    {

    }

}