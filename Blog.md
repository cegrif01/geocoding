
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

This might seem a little unorthodox, but immutable underlying data structures solve many issues with "unintentional state changes".
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
is the REST api offered by the Census Bureau.  When I first learned Domain Driven Design, I was confused as to why the Repository interfaces
were in the domain itself and not in the repositories folder.  Think of it this way... when you're designing the application
domain models need be used somehow by some type of external process.  We either want to write it to a file, save it to a database, or -- in this particular case -- use
it to consume the correct API endpoints.  The objective of this project is to take an address and convert it into latitude and longitude.

Another thing to take into consideration is that the Census Bureau may charge us for this service one day and it might be more cost
effective to use another way to perform the main objective.  Hence the RepositoryInterface.  In other words, a the RepositoryInterface means 
"I don't know how we're gonna get it done, but we are gonna get er' done".


```
<?php

namespace Geocoding\Domain;

use Geocoding\Domain\LatLong;

interface AddressDataRepositoryInterface
{

    public function fetchAddressCoordinates(Address $address) : LatLong;
}
```

Congratulations we are done with the Domain layer.  Let's move on to the Infrastructure layer now.

The config will look a bit weird so let me explain. We are using raw PHP.  I try my hardest to not couple simple packages
to frameworks.  It can be easy to get bogged down in the latest breaking changes in Laravel, Symfony, or CakePHP.  When I go to
github to find a PHP package, 8/10 of them are outdated and no good because it's coupled to an older version of Laravel.  I know I could
have just installed the Config package from Laravel and that would probably be the way you'd do it.  In those rare cases when I don't want
to use a bunch of libraries, here's a clever way to tie config values to a single class.  Of course this is immutable because I never want
my config to change from underneath me.

```
<?php

namespace Geocoding\Infrastructure\Config;

class GeocodingConfig
{
    public readonly string $censusBureauUrl;

    public readonly string $censusBureauAddressGetParam;

    public readonly string $censusBureauBenchMarkParam;

    public readonly string $censusBureauBenchMarkFormat;

    private function __construct(string $censusBureauUrl,
                                 string $censusBureauAddressGetParam,
                                 string $censusBureauBenchMarkParam,
                                 string $censusBureauBenchMarkFormat)
    {
        $this->censusBureauUrl = $censusBureauUrl;
        $this->censusBureauAddressGetParam = $censusBureauAddressGetParam;
        $this->censusBureauBenchMarkParam = $censusBureauBenchMarkParam;
        $this->censusBureauBenchMarkFormat = $censusBureauBenchMarkFormat;
    }

    public static function make(string $censusBureauUrl = 'https://geocoding.geo.census.gov/geocoder/locations/onelineaddress',
                                string $censusBureauAddressGetParam = 'address',
                                string $censusBureauBenchMarkParam = '4',
                                string $censusBureauBenchMarkFormat = 'json')  : static
    {
        return new static($censusBureauUrl,
                          $censusBureauAddressGetParam,
                          $censusBureauBenchMarkParam,
                          $censusBureauBenchMarkFormat);
    }
}
```

So I can just load my config by running

```
    //because we have default values already set, we can just call the make() method without parameters.
    //this really comes in handy
    Geoconfig::make();
```

and now I have a config object where I can access all it's values in one place. If you want to have a mock api for tests, you can add
a static method called test() with different parameters.  This allows you to encapsulate different configurations for different environments
on command.

Now moving on to the actual repository where the meat and potatoes of the application reside.  A wise coder once said (paraphrasing) "Abstraction is the art of deferring details".
That's a lot of setup to just hit a GET request on a REST api.  As applications grow in scope and data, this structure adds sanity to your life!  Most experienced developers would agree.

```
<?php

namespace Geocoding\Infrastructure\Repositories;

use Geocoding\Domain\Address;
use Geocoding\Domain\AddressDataRepositoryInterface;
use Geocoding\Domain\LatLong;
use Geocoding\Infrastructure\Config\GeocodingConfig;

/**
 * This class is responsible for hitting the Census Bureau api and returning
 * the json response of the data we will use
 */

//vendor/bin/phpunit tests/Infrastructure/Repositories/CensusBureauApiRepositoryTest.php
class CensusBureauApiRepository implements AddressDataRepositoryInterface
{
    public GeocodingConfig $geocodingConfig;

    public function __construct(GeocodingConfig $geocodingConfig)
    {
        $this->geocodingConfig = $geocodingConfig;
    }

    public function generateUrlFromAddress(Address $address) : string
    {
        $geocodeUrl = $this->geocodingConfig->censusBureauUrl;
        $addressGetParam = '?'. $this->geocodingConfig->censusBureauAddressGetParam. '=';
        $encodedAddress = $address->getUrlEncodedFullAddress() . '&';
        $benchMark = 'benchmark='. $this->geocodingConfig->censusBureauBenchMarkParam . '&';
        $format = 'format='. $this->geocodingConfig->censusBureauBenchMarkFormat;

        return $geocodeUrl. $addressGetParam. $encodedAddress. $benchMark. $format;
    }

    /**
     * Hits the geolocation api with an address and returns json response
     * of the request
     *
     * todo break this function up
     *
     * @return array
     */
    public function fetchAddressCoordinates(Address $address) : LatLong
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->generateUrlFromAddress($address));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $serverResponse = curl_exec($ch);

        curl_close($ch);

        $responseData = json_decode($serverResponse, true);

        $coordinatesArray = $responseData['result']['addressMatches'][0]['coordinates'];

        $latitude = $coordinatesArray['x'];
        $longitude = $coordinatesArray['y'];

        return new LatLong(latitude: $latitude, longitude: $longitude);
    }
}
```

Since this repository implements our AddressDataRepositoryInterface, we need a fetchAddressCoordinates(Address $address) : LatLong
method.  So you can see that's been added. Repositories hide some of the ugliest code in our applications.  For example:

```
$coordinatesArray = $responseData['result']['addressMatches'][0]['coordinates'];

$latitude = $coordinatesArray['x'];
$longitude = $coordinatesArray['y'];
```

That's ugly and can easily change if the Census Bureau updates it's api later.  However, we are given a "bench mark" number that defines
the structure of the data.  If they don't follow this convention on their end and the structure of the data changes and breaks our application,
we only have to look in one place.

Why use curl instead of a package like Guzzle? You might ask.  As explained in the section about the GeocodingConfig class,
Guzzle adds another dependency to our relatively small codebase. In my experience, the fewer composer dependencies the better. This is worth it, if I'm using POST, PUT, or DELETE endpoints.  However, in this case
since it's just a GET request we can knock this out using the PHP curl library.

One code smell that I see is

```
    public function generateUrlFromAddress(Address $address) : string
    {
        $geocodeUrl = $this->geocodingConfig->censusBureauUrl;
        $addressGetParam = '?'. $this->geocodingConfig->censusBureauAddressGetParam. '=';
        $encodedAddress = $address->getUrlEncodedFullAddress() . '&';
        $benchMark = 'benchmark='. $this->geocodingConfig->censusBureauBenchMarkParam . '&';
        $format = 'format='. $this->geocodingConfig->censusBureauBenchMarkFormat;

        return $geocodeUrl. $addressGetParam. $encodedAddress. $benchMark. $format;
    }
```

I think this is a great opportunity for a refactoring so let's do that.

todo (perform the refactoring)

```
<?php

namespace Geocoding\Infrastructure\Utils;

use Geocoding\Domain\Address;
use Geocoding\Infrastructure\Config\GeocodingConfig;

class GenerateUrlFromAddress
{
    public GeocodingConfig $geocodingConfig;

    public function __construct(GeocodingConfig $geocodingConfig)
    {
        $this->geocodingConfig = $geocodingConfig;
    }

    public function getUrl(Address $address) : string
    {
        $geocodeUrl = $this->geocodingConfig->censusBureauUrl;
        $addressGetParam = '?'. $this->geocodingConfig->censusBureauAddressGetParam. '=';
        $encodedAddress = $address->getUrlEncodedFullAddress() . '&';
        $benchMark = 'benchmark='. $this->geocodingConfig->censusBureauBenchMarkParam . '&';
        $format = 'format='. $this->geocodingConfig->censusBureauBenchMarkFormat;

        return $geocodeUrl. $addressGetParam. $encodedAddress. $benchMark. $format;
    }
}
```

Now you can use DI to inject that class into CensusBureauApiRepository

```
<?php

namespace Geocoding\Infrastructure\Repositories;

use Geocoding\Domain\Address;
use Geocoding\Domain\AddressDataRepositoryInterface;
use Geocoding\Domain\LatLong;
use Geocoding\Infrastructure\Utils\GenerateUrlFromAddress;

/**
 * This class is responsible for hitting the Census Bureau api and returning
 * the json response of the data we will use
 */

//vendor/bin/phpunit tests/Infrastructure/Repositories/CensusBureauApiRepositoryTest.php
class CensusBureauApiRepository implements AddressDataRepositoryInterface
{

    public GenerateUrlFromAddress $generateUrlFromAddress;

    public function __construct(GenerateUrlFromAddress $generateUrlFromAddress)
    {
        $this->generateUrlFromAddress = $generateUrlFromAddress;
    }

    /**
     * Hits the geolocation api with an address and returns json response
     * of the request
     */
    public function fetchAddressCoordinates(Address $address) : LatLong
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->generateUrlFromAddress->getUrl($address));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $serverResponse = curl_exec($ch);

        curl_close($ch);

        $responseData = json_decode($serverResponse, true);

        $coordinatesArray = $responseData['result']['addressMatches'][0]['coordinates'];

        $latitude = $coordinatesArray['x'];
        $longitude = $coordinatesArray['y'];

        return new LatLong(latitude: $latitude, longitude: $longitude);
    }
}
```

Now let's go the Actions/Services directory.  Some developers call them Actions others call them Services.  That's up to you.
I personally like Actions because of it's name and purpose.  Actions/Services, combine the repositories, domain models
and any other helper functions to perform "the thing".  In this case, remember "the thing" is to convert an
address into a latitude and longitude.  In the next article, we will take two sets of coordinates (lat/long) and
return the distance between them.  For now we need to convert the address into a lat/long. Repositories only exist to be used by Services/Actions, so they will always
be composed of the repository.  We will pass in an AddressDataRepositoryInterface instead of the concrete
implementation of CensusBureauApiRepository.  The way we go about getting lat/long could change.  There are hundreds if not thousands
of apis that offer this service.

```
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
```

The other reason our Actions/Services classes encapsulate the repositories is we might want to use
Actions/Services to be used in another module within our codebase.  It's important we don't access
Domain Logic or Infrastructure details from another module.  It must go through our Actions/Services
first.  So they act as a gatekeeper for a module of code.  This will make a lot more sense
when we add the distance features in the next article.

This is all for now, but let's recap.
(write recap)


In the next article we will continue to solve part 2.  We will take two LatLong classes and find the 
distance between them.