# Problem Summary

In this 2 part tutorial series, we will build a software package that uses an api to take two addresses and determine the distance apart.  This can be used in a variety of applications.  For example, determining the optimal distance in a region for a door to door sales team to travel between locations on foot.

For part 1 we will focus on converting address into Latitude and Longitude.  For part 2, we will take two Latitude and Longitude and convert them to a distance in miles or kilometers.

We could use the GoogleMaps™ api to take two addresses and determine the latitude and longitude.  It could also tell us the distance between the two locations.  However, there is a free option to perform both of these tasks.

For determining the distance between the two addresses, there are handy distance functions that can convert a Lat/Long into a distance.
If we ever find out the earth is flat for some reason, then these functions are totally wrong.  Fingers crossed.

# Let's get started

First, let’s find the api for converting addresses into Lat/Long.   I want the repo to be compatible with most versions of php.  So we will use PHP 8.1 for this project.

At the time of this writing, php8.3 is out, but I will use an older version (PHP 8.1) so it can be used just in case you haven’t updated yet.

Having worked on several projects for clients, I have noticed it's rare when they are on the bleeding edge version of php.  I will use some unique concepts throughout, but don’t worry, I’ll take the time to explain.  You can check out the github repository here: https://github.com/chazbit/geocoding.

You can download the repository locally.  I suggest you follow along.

```
git clone git@github.com:chazbit/geocoding.git

```

This repo is in compliance with the PSR-4 standard.  To perform the geocoding, we will use the free api from the good ol’ Census Bereau!  Yes, you can use their api for free (at the time of this blog).

Go to: https://geocoding.geo.census.gov/.

The documentation for their api is here: https://geocoding.geo.census.gov/geocoder/Geocoding_Services_API.html

Lets go to Red’s stadium for some baseball and then head to Bonaroo for a music festival.  The address to Reds stadium and Bonnaroo, respectively are as follows:

**Reds stadium**
100 Joe Nuxhall Way, Cincinnati, OH 45202


**Bonnaroo**
1560 New Bushy Branch Rd, Manchester, TN 37355, United States

We can go to the Census Bureau website with the Reds address:

**The link for Reds Statium:**

https://geocoding.geo.census.gov/geocoder/locations/onelineaddress?address=100%20Joe%20Nuxhall%20Way%2C%20Cincinnati%2C%20OH%2045202-5108&benchmark=4&format=json

```
{
"result": {
"input": {
"address": {
"address": "100 Joe Nuxhall Way, Cincinnati, OH 45202-5108"
},
"benchmark": {
"isDefault": true,
"benchmarkDescription": "Public Address Ranges - Current Benchmark",
"id": "4",
"benchmarkName": "Public_AR_Current"
}
},
"addressMatches": [
{
"tigerLine": {
"side": "L",
"tigerLineId": "647384196"
},
"coordinates": {
"x": -84.50827551429869,
"y": 39.09612212505558
},
"addressComponents": {
"zip": "45202",
"streetName": "JOE NUXHALL",
"preType": "",
"city": "CINCINNATI",
"preDirection": "",
"suffixDirection": "",
"fromAddress": "198",
"state": "OH",
"suffixType": "WAY",
"toAddress": "100",
"suffixQualifier": "",
"preQualifier": ""
},
"matchedAddress": "100 JOE NUXHALL WAY, CINCINNATI, OH, 45202"
}
]
}
}
```

**The link for Bonnaroo:**

https://geocoding.geo.census.gov/geocoder/locations/onelineaddress?address=1560%20New%20Bushy%20Branch%20Rd%2C%20Manchester%2C%20TN%2037355%2C%20United%20States&benchmark=4&format=json

```
{
"result": {
"input": {
"address": {
"address": "1560 New Bushy Branch Rd, Manchester, TN 37355, United States"
},
"benchmark": {
"isDefault": true,
"benchmarkDescription": "Public Address Ranges - Current Benchmark",
"id": "4",
"benchmarkName": "Public_AR_Current"
}
},
"addressMatches": [
{
"tigerLine": {
"side": "L",
"tigerLineId": "654037877"
},
"coordinates": {
"x": -86.05012799570659,
"y": 35.478336774833565
},
"addressComponents": {
"zip": "37355",
"streetName": "NEW BUSHY BRANCH",
"preType": "",
"city": "MANCHESTER",
"preDirection": "",
"suffixDirection": "",
"fromAddress": "1598",
"state": "TN",
"suffixType": "RD",
"toAddress": "1334",
"suffixQualifier": "",
"preQualifier": ""
},
"matchedAddress": "1560 NEW BUSHY BRANCH RD, MANCHESTER, TN, 37355"
}
]
}
}
```

I’m actually quite astonished that this is free!  Almost seems too good to be true.  Let’s write the interaction for this api using php.  We don’t want to couple the functionality to a particular framework, so we will use a few dependencies as possible.

At this point your directory structure should look like the following.  We will add to this as we go along but this is a good place to start:

```
geocoding
|-- .gitignore 
├── composer.json
├── LICENSE
├── README.md
│
└── public
│       └── index.php (includes the composer autoloader)
│
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
└── tests
    ├── Actions
    │     └── ConvertAddressIntoLatAndLongActionTest.php
    ├── Domain
    │     └── AddressTest.php
    │     └── LatLongTest.php
    │      
    └── Infrastructure
           └── Repositories
                   └── CensusBureauApiRepositoryTest.php
```

Let's discuss each of these files/directories in detail.  You can observe the unit tests in the repository: https://github.com/chazbit/geocoding.

The composer.json file is where the dependencies are found.  I installed a phpunit version that's compatible with php8.1.  I also like the debugging function ```dd()```.  It's a useful part of the laravel tools, so we added that one too.

**The composer.json file should look like the following:**

```
{
"name": "chazbit/geocoding",
"description": "Uses the census bureau's api to convert addresses into latitude and longitude",
"keywords": ["latitude", "longitude", "address"],
"type": "library",
"license": "MIT",
"authors": [
{
"name": "Chazbit",
"email": "charles@chazbit.com"
}
],
"autoload": {
"psr-4": {
"Geocoding\\": "src/"
}
},
"require": {
"php" : "~8.1",
"larapack/dd": "1.1"
},
"require-dev": {
"phpunit/phpunit": "~10.5.30"
},

    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    }
}

```

# The Domain:

First we'll start with the Domain directory.  I like to use data structures because they are more descriptive than PHP arrays.  At the time of this writing, C# and Java both have records which are basically readonly structs (readonly data containers with no actual functionality).  I started my career as an embedded C programmer so the concept of using structs feels more natural for me.  Even if you don't like the idea of structs, they play
more of a background role in this software package.  It's a great way to figure out what data is needed.  It also separates data from functionality.


All the fields are readonly because we want our structs (or records) to be immutable.  If this needs to be modified, we will instantiate a new copy of this struct and return it.

**AddressStruct (or you could name it AddressRecord):**

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

**LatLongStruct or LatLongRecord**

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

Since we don't have a need for persistence, we don't have "entities".  These domain models don't have an id so their equality is purely determined based on their properties. That makes these domain models value objects. Let's create them so we can make sense of how to use these structs we just created.

**Address.php**

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

**LatLong.php**

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

Each one of these value objects will validate the input before setting.  We will add validation later.  Validating value objects and entities are critical to make sure they are always in a valid state.  When we need functionality that modifies one of these value objects, then we can create a method that returns a new underlying datastructure to maintain immutability.

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

**Let's look at an example of the mutable way and how side effects can creep in:**

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
$adress1->equals($address2); //returns false because we mutated $address1 directly

```

**With an immutable address:**

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

If you don't understand why this is a big deal, then please, take the time to break this apart using various examples. This is an extremely important principle that I feel like less experienced software developers miss.  Immutable data structures aren't just associated with functional programming.  We can use this in the OOP world too!

**Domain Interfaces:**
Let's move on to the RepositoryInterface we created.  This interface belongs in our domain.  It describes how the world outside of the domain will utilize our domain models.  This should always receive domain models and/or return domain models.  In this case, the "outside world" is the REST api offered by the Census Bureau.  When I first learned Domain Driven Design, I was confused as to why the Repository interfaces were in the domain itself and not in the repositories directory.  Think of it this way-- when you're designing the system, domain models need be used somehow by some type of external process.  We either want to write it to a file, save it to a database, or, in this particular case, use it to consume the correct API endpoints.  The objective of this project is to take an address and convert it into latitude and longitude, which means, we will need to read from an external source.  The parameter passed in will be our Address class. The return type of the source would be our LatLong class.


```
<?php

namespace Geocoding\Domain;

use Geocoding\Domain\LatLong;

interface AddressDataRepositoryInterface
{

    public function fetchAddressCoordinates(Address $address) : LatLong;
}
```

Another thing to take into consideration is that the Census Bureau may charge us for this service one day it might be more cost-effective to use another way to perform the main objective.
Hence the RepositoryInterface.  In other words, the RepositoryInterface means "I don't know how we're gonna get it done, but we are gonna get er' done".  We can defer or change
implementation details as long as we have the correct parameter, and return types.

Congratulations we are done with the Domain layer.  Let's move on to the Infrastructure layer now.

# Configuration
The config will look a bit weird so let me explain. We are using raw PHP.  I try my hardest to not couple simple packages to a specific framework.  It can be easy to get bogged down in the latest breaking changes in Laravel, Symfony, or CakePHP.  When I go to github to find a PHP package, 8/10 of them are outdated and no good because it's coupled to an older version of Laravel or Symfony.  I know I could have just installed the Config package from Laravel and that would probably be the way you'd do it.  In those rare cases when I don't want to use a bunch of libraries, here's a clever way to tie config values to a single class.  Of course this is immutable because I never want my config to change it's properties at run time.

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

Now I have a config object where I can access all it's values in one place. If you want to have a mock api for tests, you can add a static method called test() with different parameters.  This allows you to encapsulate different configurations for different environments
on command.

Now moving on to the actual repository where the meat and potatoes of the application reside.  A wise coder once said (paraphrasing) "Abstraction is the art of deferring details".
That's a lot of setup to just hit a GET request on a REST api.  As applications grow in scope and data, this structure adds sanity to your life!  Most experienced developers would agree.

**The Repository in the Infrastruture layer**

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

Since this repository implements our AddressDataRepositoryInterface, we need a ```fetchAddressCoordinates(Address $address) : LatLong``` method.  So you can see that's been added. Repositories hide some of the ugliest code in our applications.  

**For example:**

```
$coordinatesArray = $responseData['result']['addressMatches'][0]['coordinates'];

$latitude = $coordinatesArray['x'];
$longitude = $coordinatesArray['y'];
```

That's ugly and can easily change if the Census Bureau updates it's api later.  However, we are given a "benchmark" number that defines
the structure of the data.  If they don't follow this convention on their end and the structure of the data changes and breaks our application, we only have to look in one place.

*Why use curl instead of a package like Guzzle?* You might ask.  As explained in the section about the GeocodingConfig class, Guzzle adds another dependency to our relatively small codebase. In my experience, the fewer composer dependencies the better. This is worth it, if I'm using POST, PUT, or DELETE endpoints.  However, in this case, since it's just a GET request, we can knock this out using the PHP curl library.

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

**Refactoring by moving the Url Generation process to it's own class:**

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

Now you can use Dependency Injection to pass that class into CensusBureauApiRepository

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

# Actions/Services:

Now let's go the Actions/Services directory.  Some developers call them Actions others call them Services.  That's up to you. I personally like Actions because it implies the actual doing of an essential piece of code.  Actions/Services, combine the repositories, domain models, and any other helper functions to perform "the thing".  In this case, remember "the thing" is to convert an address into a latitude and longitude.  In the next article, we will take two sets of coordinates (lat/long) and return the distance between them.  For now, we need to convert the address into a lat/long. 


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
Repositories only exist to be used by Services/Actions, so they will always be composed of the repository.  We will pass in an AddressDataRepositoryInterface into the constructor of the Action instead of the concrete implementation of CensusBureauApiRepository.  The way we go about getting lat/long could change.  There are hundreds if not thousands of apis that offer this service.

The other reason our Actions/Services classes encapsulate the repositories is we might want to use Actions/Services to be used in another module within our codebase.  It's important we don't access
Domain Logic or Infrastructure details from another module.  It must go through our Actions/Services first.  So they act as a gatekeeper for a module of code.  This will make a lot more sense when we add the distance features in the next article.

# Pain Points
Let's take the time to go over some pain points and how to fix them.  The first let's address the super awkward calling of the ConvertAddressIntoLatAndLongAction.php class.  In order to instantiate this class, you'll need to do something like this:

```
        /** @var ConvertAddressIntoLatAndLongAction $addressConverter */
        $addressConverterAction = (new ConvertAddressIntoLatAndLongAction(new CensusBureauApiRepository(new GenerateUrlFromAddress(GeocodingConfig::make()))));
```

Let's make this a bit easier.  It would be nice to just use a static method that hides the booting details

```
<?php

namespace Geocoding\Actions;

use Geocoding\Domain\Address;
use Geocoding\Domain\AddressDataRepositoryInterface;
use Geocoding\Domain\LatLong;
use Geocoding\Infrastructure\Config\GeocodingConfig;
use Geocoding\Infrastructure\Repositories\CensusBureauApiRepository;
use Geocoding\Infrastructure\Utils\GenerateUrlFromAddress;

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
```

With this refactoring, we could just call ```ConvertAddressIntoLatAndLongAction::for($address);``` this is much easier to deal with.  In some controller where this might be used, you can just call this statically without having to worry about confusing instantiation.

# Validation
The other thing we need to do is validate the input coming into our domain models (Address and LatLong).  According to the rules of value objects in Domain Driven Design (DDD), we must always have them in a valid state.  So let's modify Address first to validate if what's being passed in is a valid address

```
<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\AddressStruct;
use Geocoding\Domain\Exceptions\InvalidAddressException;

//vendor/bin/phpunit tests/Domain/AddressTest.php
class Address
{
    public readonly AddressStruct $addressStruct;

    /**
     * @throws InvalidAddressException
     */
    public function __construct(string $country,
                                string $city,
                                string $state,
                                string $street,
                                string $zip)
    {
        $this->addressStruct = new AddressStruct($country, $city, $state, $street, $zip);

        /** @throws InvalidAddressException */
        $this->validate();
    }

    /**
     * @throws InvalidAddressException
     */
    private function validate() : void
    {

        if(!preg_match('/[a-zA-Z ]+/', $this->addressStruct->country)) {
            throw new InvalidAddressException($this->addressStruct->country. ' is not a valid country');
        }

        if(!preg_match('/[\d]+ [a-zA-Z ]+/',$this->addressStruct->street)) {
            throw new InvalidAddressException($this->addressStruct->street. ' is not a valid street');
        }

        if(!preg_match('/[a-zA-Z ]+/',$this->addressStruct->city)) {
            throw new InvalidAddressException($this->addressStruct->city. ' is not a valid city');
        }

        if(!preg_match('/[a-zA-Z ]+/',$this->addressStruct->state)) {
            throw new InvalidAddressException($this->addressStruct->state. ' is not a valid state');
        }

        if(!preg_match('/[\d]{5}(-[\d]{4})?/',$this->addressStruct->zip)) {
            throw new InvalidAddressException($this->addressStruct->zip.  ' is not a valid zip code');
        }
    }

    public function getFullAddress() : string
    {
        return $this->addressStruct->street . ', ' .
               $this->addressStruct->city . ', '.
               $this->addressStruct->state . ' '.
               $this->addressStruct->zip;
    }

    public function getUrlEncodedFullAddress() : string
    {
        return rawurlencode($this->getFullAddress());
    }
}
```

Now for the LatLong value object

```
<?php

namespace Geocoding\Domain;

use Geocoding\Domain\DataStructures\LatLongStruct;
use Geocoding\Domain\Exceptions\InvalidLatitudeAndLongitudeException;

//vendor/bin/phpunit tests/Domain/LongLatTest.php
class LatLong
{
    private readonly LatLongStruct $latLongStruct;

    /**
     * @throws InvalidLatitudeAndLongitudeException
     */
    public function __construct(string $latitude, string $longitude)
    {
        $this->latLongStruct = new LatLongStruct(latitude: $latitude, longitude: $longitude);

        $this->validate();
    }

    private function validate()
    {
        if(!preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/',$this->getLatitude())) {
            throw new InvalidLatitudeAndLongitudeException($this->getLatitude(). ' is not a valid latitude');
        }

        if(!preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',$this->getLongitude())) {
            throw new InvalidLatitudeAndLongitudeException($this->getLongitude(). ' is not a valid longitude');
        }

        if( !(($this->getLatitude() >= -90) && ($this->getLatitude() <= 90)) ) {
            throw new InvalidLatitudeAndLongitudeException($this->getLatitude() . ' is an invalid latitude coordinate');
        }

        if( !(($this->getLongitude() >= -180) && ($this->getLongitude() <= 180)) ) {
            throw new InvalidLatitudeAndLongitudeException($this->getLongitude() . ' is an invalid longitude coordinate');
        }
    }

    public function getLatitude() : string
    {
        return $this->latLongStruct->latitude;
    }

    public function getLongitude() : string
    {
        return $this->latLongStruct->longitude;
    }
}
```

These validators are rather complex.  They could be split into a trait, but that's up to you.  I think the point has been made for keeping value objects in valid states.  This rule comes in handy when dealing with form validation.  If all of our objects are validated as they are instantiated, even if you forget a validation rule at the Controller level, the hard work is already done for you.  You can just bubble the exception up to the controller and display it to the user.

*Now our revised directory structure looks like the following*

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
    │      └── Exceptions
    │              └── InvalidAddressException.php
    │              └── InvalidLatitudeAndLongitudeException.php
    │     └── Address.php
    │     └── LatLong.php
    │     └── AddressDataRepositoryInterface.php
    │      
    └── Infrastructure
           └── Config
                   └── GeocodingConfig.php
           └── Utils
                   └── GenerateUrlFromAddress.php
           └── Repositories
                   └── CensusBureauApiRepository.php
```

# Conclusion

In this tutorial, we leveraged the power of DDD to solve a meaningful problem.  This tutorial will also be on my github repo https://github.com/chazbit/geocoding.  We started off by building our domain models, which in this case was just two value objects: Address and LatLong.

We used the repository pattern to encapsulate the complex details of dealing with the Census Bureau Api that handles curl interaction.  We used a config file GeocodingConfig that has a static helper method called make() that will use it's config defaults without the client (the consumer of the config api) having to instantiate it.

Finally, we created an Action/Service class called ```ConvertAddressIntoLatAndLongAction```, in our application layer.  This class works in conjuction with the CensusBureauApi to return the results in terms of a domain model.  In our case this is the LatLong domain model.

## To Be Continued... Part 2 will handle Distance conversions
In the next article we will continue to solve part 2.  We will take two LatLong classes and find the distance between them.  We will also discuss modules... Stay Tuned!