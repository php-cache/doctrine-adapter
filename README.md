# Doctrine PSR-6 adapter

This is a implementation for the PSR-6 that wrapps the doctrine cache

## Usage

```php
use Doctrine\Common\Cache\MemcachedCache;
use namespace Cache\Doctrine\CachePool;

$doctrineCache = new MemcachedCache();
$psr6Cache = new CachePool($doctrineCache);

/** @var CacheItemInterface $item */
$item = $psr6Cache->getItem('key');


```