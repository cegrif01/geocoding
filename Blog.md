
I’m actually quite astonished that this is free!  Almost seems too good to be true.  Let’s write this api using php.  We don’t want to couple the functionality to a particular framework.

At this point your directory structure should look like the following:

```
geocoding
|-- .gitignore 
├── composer.json
├── LICENSE
├── README.md
└── src
    ├── Actions
    │     └── ConvertAddressIntoLatAndLongAction.php
    ├── Domain
    │     └── DataStructures
    │              └── AddressStruct.php
    │              └── LatLongStruct.php
    │     └── Address.php
    │     └── LatLong.php
    │     └── AddressDataRepositoryInterface.php
    │      
    └── Infrastructure
           └── Config
                   └── GeocodingConfig.php
           └── Repositories
                   └── CensusBureauApiRepository.php
```