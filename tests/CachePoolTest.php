<?php

/*
 * This file is part of php-cache\doctrine-adapter package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\Adapter\Doctrine\Tests;

use Cache\Adapter\Common\CacheItem;
use Cache\Adapter\Common\HasExpirationDateInterface;
use Cache\Adapter\Doctrine\DoctrineCachePool;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FlushableCache;
use Mockery as m;
use Mockery\MockInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CachePoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type DoctrineCachePool
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
        $this->mockItem     = m::mock(CacheItem::class);
        $this->mockDoctrine = m::mock(Cache::class);

        $this->pool = new DoctrineCachePool($this->mockDoctrine);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(DoctrineCachePool::class, $this->pool);
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

        $this->mockDoctrine->shouldReceive('fetch')->with('non_item_key')->andReturnNull();
        $this->assertInstanceOf(CacheItemInterface::class, $this->pool->getItem('non_item_key'));
    }

    public function testGetTagItem()
    {
        $this->mockDoctrine->shouldReceive('fetch')->with('test_key')->andReturn($this->mockItem);

        $this->assertEquals($this->mockItem, $this->pool->getItem('test_key'));

        $this->mockDoctrine->shouldReceive('fetch')->with('non_item_key')->andReturnNull();
        $this->assertInstanceOf(CacheItemInterface::class, $this->pool->getItem('non_item_key'));
    }

    public function testGetItems()
    {
        $itemOne = m::mock(CacheItemInterface::class);
        $itemTwo = m::mock(CacheItemInterface::class);

        $this->mockDoctrine->shouldReceive('fetch')->andReturn($itemOne);
        $this->mockDoctrine->shouldReceive('fetch')->andReturn($itemTwo);

        $this->assertEquals(['1' => $itemOne, '2' => $itemTwo], $this->pool->getItems(['1', '2']));
    }

    public function testHasItem()
    {
        $this->mockItem->shouldReceive('isHit')->twice()->andReturn(false, true);
        $this->mockDoctrine->shouldReceive('fetch')->andReturn($this->mockItem);
        $this->mockDoctrine->shouldReceive('delete')->with('bad_key')->andReturn(true);

        $this->assertFalse($this->pool->hasItem('bad_key'));
        $this->assertTrue($this->pool->hasItem('good_key'));
    }

    public function testClear()
    {
        $this->assertFalse($this->pool->clear());

        $cache = m::mock(Cache::class.','.FlushableCache::class);
        $cache->shouldReceive('flushAll')->andReturn(true);

        $newPool = new DoctrineCachePool($cache);
        $this->assertTrue($newPool->clear());

        $cache->shouldReceive('fetch');
        $cache->shouldReceive('save');
        $this->assertTrue($newPool->clear(['dummy_tag']));
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

        $this->mockDoctrine->shouldReceive('delete')->twice()->andReturn(true, false);

        $this->assertFalse($this->pool->deleteItems(['1', '2']));
    }

    public function testSave()
    {
        $item = m::mock(CacheItemInterface::class);
        $item->shouldReceive('getKey')->withNoArgs()->andReturn('test_key');
        $this->mockDoctrine->shouldReceive('save')->with('test_key', $item, 0)->andReturn(true);

        $this->assertTrue($this->pool->save($item));

        $date = m::mock(\DateTime::class);
        $date->shouldReceive('getTimestamp')->withNoArgs()->andReturn(time() + 1);
        $item = m::mock(CacheItemInterface::class.', '.HasExpirationDateInterface::class);
        $item->shouldReceive('getExpirationDate')->withNoArgs()->andReturn($date);
        $item->shouldReceive('getKey')->withNoArgs()->andReturn('test_key_2');
        $this->mockDoctrine->shouldReceive('save')->with('test_key_2', $item, 1)->andReturn(true);

        $this->assertTrue($this->pool->save($item));
    }
}
