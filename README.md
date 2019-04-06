# BigDataCloud PHP API Client

[![Packagist](https://img.shields.io/packagist/v/bigdatacloudapi/php-api-client.svg)](https://packagist.org/packages/bigdatacloudapi/php-api-client)

A simple CURL wrapper API for connecting to BigDataCloud API services.


## Installation via Composer

  `composer require bigdatacloudapi/php-api-client`


## Example usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$apiKey = "XXX"; // Your api key found at: https://www.bigdatacloud.net/customer/account

$client = new \BigDataCloud\Api\Client($apiKey);


/*
Can specify the api endpoint using either camelised magic methods, or by calling the communicate command directly.
Example magic method: "GET" from "country-info" endpoint becomes: getCountryInfo();
*/

//$result=$client->communicate('country-info','GET',['code'=>'AU']);

$result=$client->getCountryInfo(['code'=>'AU']);

var_dump($result);
```


## Example output

```
array(12) {
  ["isoAlpha2"]=>
  string(2) "AU"
  ["isoAlpha3"]=>
  string(3) "AUS"
  ["m49Code"]=>
  int(36)
  ["isoName"]=>
  string(9) "Australia"
  ["isoAdminLanguages"]=>
  array(1) {
    [0]=>
    array(3) {
      ["isoAlpha3"]=>
      string(3) "eng"
      ["isoAlpha2"]=>
      string(2) "en"
      ["isoName"]=>
      string(7) "English"
    }
  }
  ["unRegion"]=>
  string(33) "Oceania/Australia and New Zealand"
  ["currency"]=>
  array(4) {
    ["numericCode"]=>
    int(36)
    ["code"]=>
    string(3) "AUD"
    ["name"]=>
    string(17) "Australian Dollar"
    ["minorUnits"]=>
    int(2)
  }
  ["wbRegion"]=>
  array(3) {
    ["id"]=>
    string(3) "EAS"
    ["iso2Code"]=>
    string(2) "Z4"
    ["value"]=>
    string(19) "East Asia & Pacific"
  }
  ["wbIncomeLevel"]=>
  array(3) {
    ["id"]=>
    string(3) "HIC"
    ["iso2Code"]=>
    string(2) "XD"
    ["value"]=>
    string(11) "High income"
  }
  ["callingCode"]=>
  string(2) "61"
  ["countryFlagEmoji"]=>
  string(8) "🇦🇺"
  ["__apiResponseTime"]=>
  float(1526.09)
}


```
