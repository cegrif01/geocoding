
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

Let's discuss each of these files/directories in detail.

First we'll start with the Domain directory.  I like to use datastructures because they are more descriptive than PHP arrays.
At the time of this writing, C# and Java both have records which are basically structs (readonly data containers with no actual functionality).  I started my career as an embedded
C programmer so the concept of using structs feels natural for me.  Even if you don't like the idea of structs, they play
more of a background role in this software package.  It's a great way to figure out what data is needed.

AddressStruct or AddressRecord:

```
<?php

namespace Geocoding\Domain\DataStructures;

class AddressStruct
{
    public readonly string $country;
    public readonly string $city;
    public readonly string $state;
    public readonly string $street;
    public readonly string $zip;

    public function __construct(string $country,
                                string $city,
                                string $state,
                                string $street,
                                string $zip)
    {
        $this->country = $country;
        $this->city = $city;
        $this->state = $state;
        $this->street = $street;
        $this->zip = $zip;
    }
}
```

All the fields are readonly because we want our structs (or records) to be immutable.  If this needs to be modified, we will instantiate
a new copy of this struct and return it.

LatLongStruct or LatLongRecord

```
<?php

namespace Geocoding\Domain\DataStructures;

class LatLongStruct
{

    public readonly string $latitude;

    public readonly string $longitude;

    public function __construct(string $latitude, string $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}
```

Since we don't have a need for persistence, we don't have "entities".  We instead have value objects.
Let's create them so we can make sense of these structs we just created.

Address.php

```
<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\AddressStruct;

class Address
{
    public readonly AddressStruct $addressStruct;

    public function __construct(string $country,
                                string $city,
                                string $state,
                                string $street,
                                string $zip)
    {
        //validate the input
        $this->addressStruct = new AddressStruct($country, $city, $state, $street, $zip);
    }
}
```

LatLong.php

```
<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\LatLongStruct;

class LatLong
{
    private readonly LatLongStruct $latLongStruct;

    public function __construct(string $latitude, string $longitude)
    {
        //validate the input
        $this->latLongStruct = new LatLongStruct(latitude: $latitude, longitude: $longitude);
    }
}
```

Each one of these value objects will validate the input before setting.  Value objects and entities should always be in a valid state.
When we need functionality that modifies one of these value objects, then we can create a method that returns a new underlying datastructure.
For example if we want to update the city, we can put this method in the Address class.

```
    public function setCity(string $city) : Address
    {
         return new Address($this->addressStruct->country,
                            $city,
                            $this->addressStruct->state,
                            $this->addressStruct->street,
                            $this->addressStruct->zip,
        );
    }
```

This might seem a little unorthodox, but immutable underlying data structures solves many issues with "unintentional state changes".
In my experiences with OOP, having a solution for unintentional state changes is much appreciated, even though, it might not be totally apparent why that's necessary.

Let's look at an example of the mutable way and how side effects can creep in:

```
$address1 = new Address('USA', 'Cincinnati', 'OH', '555 Something St.', '40205');

$address2 = new Address('USA', 'Cincinnati', 'OH', '555 Something St.', '40205');

$adress1->equals($address2); //returns true


public function someMethodThatChangesCity(Address $address) : Address
{
    //some logic
    
    //changes address by modifying the instance directly (mutable)
    $address->setCity('San Diego');
    
    return $address;
}

$addressReturnedFromService = $this->serviceClass->someMethodThatChangesCity($address1);


$addressReturnedFromService->equals($address1); //returns true
$adress1->equals($address2); //returns false

```

With an immutable address

```
$address1 = new Address('USA', 'Cincinnati', 'OH', '555 Something St.', '40205');

$address2 = new Address('USA', 'Cincinnati', 'OH', '555 Something St.', '40205');

$adress1->equals($address2); //returns true


public function someMethodThatChangesCity(Address $address) : Address
{
    //some logic
    
    //changes address but returns a new instance instead of modifying the address passed in (immutable)
    $address->setCityButReturnsNewAddress('San Diego');
    
    return $address;
}

$addressReturnedFromService = $this->serviceClass->someMethodThatChangesCity($address1);


$addressReturnedFromService->equals($address1); //false

//since the $address1 object is never modified, it still equals to the original
$adress1->equals($address2); //returns true
```

If you don't understand why this is a big deal, then please, take the time to break this apart using various examples. This is an extremely important principle
that I feel like less experienced software developers miss.

Let's move on to the RepositoryInterface we created.  This interface belongs in our domain.  It describes how the outside world
will use our domain models.  This should always receive domain models and/or return domain models.  In this case, the "outside world"
is the REST api offered by the Census Bureau


```
<?php

namespace Geocoding\Domain;

use Geocoding\Domain\LatLong;

interface AddressDataRepositoryInterface
{

    public function fetchAddressCoordinates(Address $address) : LatLong;
}
```

