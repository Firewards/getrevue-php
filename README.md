# GetRevue PHP
Unofficial GetRevue PHP API for [v2](https://www.getrevue.co/api).

This package makes it simple to access GetRevues's web API. Checkout [https://www.getrevue.co/api](https://www.getrevue.co/api) for more information on GetRevue's API.

[![Source Code](https://img.shields.io/badge/source-getrevue--php-blue)](https://github.com/Firewards/getrevue-php)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/Firewards/getrevue-php/blob/master/LICENSE)

## Install

Via Composer

``` bash
$ composer require firewards/getrevue-php
```

## Requirements

The following versions of PHP are supported.

* PHP 7.0
* PHP 7.1
* PHP 7.2
* PHP 7.3
* PHP 7.4

### API Key
All API calls require an API Key. You can find your API Key in the GetRevue Account page under the Integrations tab (scroll all the way down).

## Usage
Start by using GetRevueAPI and creating an instance with your ConvertKit API key
```php
$apiKey = "your-key-goes-here";
$api = new \Firewards\GetRevueApi($apiKey);
```
### Examples

Add a new email to your list of subscribers.
```php
$subscriber = $this->api->addSubscriber("getrevue@firewards.com", 'firstName', 'lastName');

/**
Return example:
array(6) {
  ["id"]=>
  int(42840832)
  ["list_id"]=>
  int(64471)
  ["email"]=>
  string(26) "test_8658431@firewards.com"
  ["first_name"]=>
  string(9) "firstName"
  ["last_name"]=>
  string(8) "lastName"
  ["last_changed"]=>
  string(24) "2020-12-24T02:21:26.333Z"
}
 */
```

Unsubscribe an existing subscriber by email. *Revue is not returning any success or failure codes.*
```php
if ($api->unsubscribe("getrevue@firewards.com")) {
    // unsbuscribed
}
```

Get all subscribers and iterate through them. *Revue's API does not support paging, yet. The API will return 5000 subscribers.*
```php
foreach ($api->getSubscribers() as $subscriber) {
    /*
     * array(6) {
          ["id"]=>
          int(42840832)
          ["list_id"]=>
          int(64471)
          ["email"]=>
          string(26) "test_8658431@firewards.com"
          ["first_name"]=>
          string(9) "firstName"
          ["last_name"]=>
          string(8) "lastName"
          ["last_changed"]=>
          string(24) "2020-12-24T02:21:26.333Z"
        }
     */
}
```

Get all available lists (subscriber groups).
```php
foreach ($api->getLists()) {
    
}
```

Return information about a specific list using its id.
```php
$list = $api->getList(13212);
```

## License

The MIT License (MIT). Please see [License File](https://github.com/Firewards/getrevue-php/blob/master/LICENSE) for more information.

## Sponsor

![www.firewards.com](https://www.firewards.com/assets/images/Logo.png)

This package is sponsored by [www.firewards.com](https://www.firewards.com), Firewards makes it easy to setup a referral and rewards program for your email list and newsletter.
