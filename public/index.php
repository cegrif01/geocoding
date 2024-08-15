<?php

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'Geocoding.inc.php';

use Geocoding\DataStructures\AddressStruct;

$addressStruct = new AddressStruct();

var_dump(($addressStruct));
die;