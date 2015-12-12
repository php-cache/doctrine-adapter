<?php

namespace Cache\Doctrine\Tests;

use Cache\Doctrine\CachePool;
use Cache\IntegrationTests\CachePoolTest as BaseTest;
use Doctrine\Common\Cache\ArrayCache;

class PoolFunctionTest extends BaseTest
{
    function createCachePool()
    {
        $doctrineCache = new ArrayCache();

        return new CachePool($doctrineCache);
    }
}