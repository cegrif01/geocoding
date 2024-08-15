<?php

// @codeCoverageIgnoreStart
$includes = array(
    'src/Actions/ConvertAddressIntoLatAndLongAction.php',
    'src/Actions/DataStructures/AddressStruct.php',
    'src/Actions/DataStructures/LocationStruct.php',
    'src/InfraStructure/Config/GeocodingConfig.php',

);

foreach ($includes as $file) {
    require_once dirname(__FILE__) . '/' . $file;
}
// @codeCoverageIgnoreEnd