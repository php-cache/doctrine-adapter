# Doctrine PSR-6 adapter 
[![Build Status](https://travis-ci.org/php-cache/doctrine-adapter.svg?branch=master)](https://travis-ci.org/php-cache/doctrine-adapter) [![codecov.io](https://codecov.io/github/php-cache/doctrine-adapter/coverage.svg?branch=master)](https://codecov.io/github/php-cache/doctrine-adapter?branch=master)

This is a implementation for the PSR-6 that wraps the Doctrine cache. This implementation supports tags. 

If you want to use this library with Symfony you may be intrerested in
[Doctrine Adapter Bundle](https://github.com/php-cache/doctrine-adapter-bundle). 


## Usage

```php
use Doctrine\Common\Cache\MemcachedCache;
use Cache\Doctrine\CachePool;

// Create a instance of Doctrine's MemcachedCache
$memcached = new \Memcached();
$memcached->addServer('localhost', 11211);
$doctrineCache = new MemcachedCache();
$doctrineCache->setMemcached($memcached);

// Wrap Doctrine's cache with the PSR-6 adapter
$pool = new CachePool($doctrineCache);

/** @var CacheItemInterface $item */
$item = $pool->getItem('key');
```

## Tagging

The `CachePool` implements `Cache\Taggable\TaggablePoolInterface` from [Taggable Cache](https://github.com/php-cache/taggable-cache). 
Below is an example of how you could use tags: 

```php

$item = $pool->getItem('tobias', ['person']);
$item->set('foobar');
$pool->save($item);

$item = $pool->getItem('aaron', ['person', 'developer']);
$item->set('foobar');
$pool->save($item);

$pool->getItem('tobias', ['person'])->isHit(); // true
$pool->getItem('aaron', ['person', 'developer'])->isHit(); // true

// Clear all cache items tagged with 'developer'
$pool->clear(['developer']);

$pool->getItem('tobias', ['person'])->isHit(); // true
$pool->getItem('aaron', ['person', 'developer'])->isHit(); // false
```

See more example and understand how you use tags here: https://github.com/php-cache/taggable-cache
