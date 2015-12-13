# Doctrine PSR-6 adapter [![Build Status](https://travis-ci.org/php-cache/doctrine-adapter.svg?branch=master)](https://travis-ci.org/php-cache/doctrine-adapter)[![codecov.io](https://codecov.io/github/php-cache/doctrine-adapter/coverage.svg?branch=master)](https://codecov.io/github/php-cache/doctrine-adapter?branch=master)

This is a implementation for the PSR-6 that wraps the doctrine cache


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
$psr6Cache = new CachePool($doctrineCache);

/** @var CacheItemInterface $item */
$item = $psr6Cache->getItem('key');
```