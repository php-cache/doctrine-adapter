# Doctrine PSR-6 adapter 

This is a implementation for the PSR-6 that wrapps the doctrine cache

#### Badges

| Service | Badge |
| ------- | ----- |
 Travis   | [![Build Status](https://travis-ci.org/php-cache/doctrine-adapter.svg?branch=master)](https://travis-ci.org/php-cache/doctrine-adapter)
CodeCoverage | [![codecov.io](https://codecov.io/github/php-cache/doctrine-adapter/coverage.svg?branch=master)](https://codecov.io/github/php-cache/doctrine-adapter?branch=master)

## Usage

```php
use Doctrine\Common\Cache\MemcachedCache;
use namespace Cache\Doctrine\CachePool;

$doctrineCache = new MemcachedCache();
$psr6Cache = new CachePool($doctrineCache);

/** @var CacheItemInterface $item */
$item = $psr6Cache->getItem('key');


```
