<?php

/*
 * This file is part of php-cache\doctrine-adapter package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\Doctrine\Tests;

use Cache\Doctrine\CachePool;
use Cache\IntegrationTests\TaggableCachePoolTest;
use Doctrine\Common\Cache\ArrayCache;

class TagIntegrationTest extends TaggableCachePoolTest
{
    public function createCachePool()
    {
        $doctrineCache = new ArrayCache();

        return new CachePool($doctrineCache);
    }
}
