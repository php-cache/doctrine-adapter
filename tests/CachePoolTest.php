<?php

/**
 * This file is part of php-cache
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Cache\Doctrine\Tests;

use Cache\Doctrine\CacheItem;
use Cache\Doctrine\CachePool;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FlushableCache;
use Mockery as m;
use Mockery\MockInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CachePoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type CachePool
     */
    private $pool;

    /**
     * @type MockInterface|CacheItem
     */
    private $mockItem;

    /**
     * @type MockInterface|Cache
     */
    private $mockDoctrine;

    protected function setUp()
    {
        $this->mockItem = m::mock(CacheItem::class);
        $this->mockDoctrine = m::mock(Cache::class);

        $this->pool = new CachePool($this->mockDoctrine);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(CachePool::class, $this->pool);
        $this->assertInstanceOf(CacheItemPoolInterface::class, $this->pool);
    }

    public function testGetCache()
    {
        $this->assertInstanceOf(Cache::class, $this->pool->getCache());
        $this->assertEquals($this->mockDoctrine, $this->pool->getCache());
    }

    public function testGetItem()
    {
        $this->mockDoctrine->shouldReceive('fetch')->with('test_key')->andReturn($this->mockItem);

        $this->assertEquals($this->mockItem, $this->pool->getItem('test_key'));
    }

    public function testGetItems()
    {
        $itemOne = m::mock(Cache::class);
        $itemTwo = m::mock(Cache::class);

        $this->mockDoctrine->shouldReceive('fetch')
            ->twice()
            ->andReturn($itemOne, $itemTwo);

        $this->assertEquals(['1' => $itemOne, '2' =>$itemTwo], $this->pool->getItems(['1', '2']));
    }

    public function testHasItem()
    {
        $this->mockItem->shouldReceive('isExpired')->times(3)->andReturn(false, true, false);
        $this->mockItem->shouldReceive('isHit')->times(2)->andReturn(false, true);
        $this->mockDoctrine->shouldReceive('fetch')->andReturn($this->mockItem);
        $this->mockDoctrine->shouldReceive('delete')->with('bad_key')->andReturn(true);

        $this->assertFalse($this->pool->hasItem('bad_key'));
        $this->assertFalse($this->pool->hasItem('bad_key'));
        $this->assertTrue($this->pool->hasItem('good_key'));
    }

    public function testClear()
    {
        $this->assertFalse($this->pool->clear());

        $cache = m::mock(Cache::class .','. FlushableCache::class);
        $cache->shouldReceive('flushAll')->andReturn(true);

        $newPool = new CachePool($cache);
        $this->assertTrue($newPool->clear());
    }

    public function testDeleteItem()
    {
        $this->mockDoctrine->shouldReceive('delete')->with('key')->andReturn(true);

        $this->assertTrue($this->pool->deleteItem('key'));
    }

    public function testDeleteItems()
    {
        $this->mockDoctrine->shouldReceive('delete')->twice()->andReturn(true);

        $this->assertTrue($this->pool->deleteItems(['1', '2']));
    }

    public function testSave()
    {

    }

    public function testSaveDeferred()
    {

    }

    public function testCommit()
    {

    }
}
