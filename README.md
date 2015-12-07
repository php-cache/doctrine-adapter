# Doctrine PSR-6 adapter

This is a implementation for the PSR-6 that wrapps the doctrine cache

## Usage

```php

$doctrineCache = new FilesystemCache('/path/to-cache-dir');
$psr6Cache = new CachePoolItem($doctrineCache);

/** @var CacheItemInterface $item */
$item = $psr6Cache->getItem('key');


```