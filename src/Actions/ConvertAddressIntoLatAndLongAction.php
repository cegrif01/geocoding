<?php

namespace Geocoding\Actions;

use Geocoding\Domain\Address;
use Geocoding\Domain\AddressDataRepositoryInterface;
use Geocoding\Domain\LatLong;
use Geocoding\Infrastructure\Config\GeocodingConfig;
use Geocoding\Infrastructure\Repositories\CensusBureauApiRepository;
use Geocoding\Infrastructure\Utils\GenerateUrlFromAddress;

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

    /**
     * Convenient, bootable static method that makes calls to this action painless.
     */
    public static function for(Address $address) : LatLong
    {
        /** @var ConvertAddressIntoLatAndLongAction $addressConverter */
        $addressConverterAction = (new ConvertAddressIntoLatAndLongAction(
                                            new CensusBureauApiRepository(
                                                new GenerateUrlFromAddress(GeocodingConfig::make()))));

        return $addressConverterAction($address);
    }
}