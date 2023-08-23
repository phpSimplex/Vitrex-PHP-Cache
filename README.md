Vitrex PHP Cache
================
[![Version](https://img.shields.io/badge/Version-1.0.1-green?style=for-the-badge)]()
[![License](https://img.shields.io/badge/License-MIT-yellowgreen?style=for-the-badge)]()
[![Donate to this project using PayPal](https://img.shields.io/badge/PayPal-Donate-blue?style=for-the-badge)](https://www.paypal.me/TKivits )

OVERVIEW
--------
This is a simple PHP Cache Abstraction Layer for PHP 7.4 that provides a
simple interaction with several cache mechanism.

`Vitrex PHP Cache` provides the ability to cache frequently accessed content via several different adapters.
Depending on the server environment and what's available, an application can use one of the following
cache adapters:

* File (directory on disk)
* Memcache (cache service)
* Session (short-term caching in session)

INSTALL
-------
Download the latest version from here.

Install `Vitrex PHP Cache` using Composer.

    composer require vitrexphp/cache

BASIC USAGE
-----------

### Setting up the different cache object adapters
```php
<?php

use Vitrex\Cache\Cache;
use Vitrex\Cache\Adapter;

$File = new Adapter\File(__DIR__);
$Session = new Adapter\Session();
$MemCached = new Adapter\Memcached();

/* Then inject one of the adapters into the main cache object */
$Cache = new Cache($File);
```

### Save and load data from cache

Once a cache object is created, you can simply save and load data from it like below:
```php
<?php
if (($cacheData = $Cache->load('Foo')) === false) {
	$cacheData = [
		'Name'     => 'Vitrex PHP Cache',
		'Class'    => Cache::class,
		'LifeTime' => Adapter\Adapter::LIFE_TIME_1_WEEK,
		'Foo'      => 'Bar'
	];
	$Cache->save('Foo', $cacheData, '1 WEEK');
}
var_dump($cacheData);
?>
```

### Deleting cache file

```php
$Cache->delete('Foo');
```

### Clear all cache files
```php
$Cache->clearAll();
```

SUPPORT
-------
For support please visit: [Github](https://github.com/phpSimpex/Vitrex-PHP-Cache) | [Issues](https://github.com/phpSimplex/Vitrex-PHP-Cache/issues)
For donations please visit: [PayPal](https://paypal.me/TKivits)
For professional support please contact [by e-mail](mailto:tommykivits@gmail.com).
