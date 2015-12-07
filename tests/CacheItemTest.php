<?php

namespace Cache\Doctrine\Tests;

use Cache\Doctrine\CacheItem;
use Psr\Cache\CacheItemInterface;

class CacheItemTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $item = new CacheItem('test_key');

        $this->assertInstanceOf(CacheItem::class, $item);
        $this->assertInstanceOf(CacheItemInterface::class, $item);
    }

    public function testGetKey()
    {
        $item = new CacheItem('test_key');
        $this->assertEquals('test_key', $item->getKey());
    }

    public function testSet()
    {
        $item = new CacheItem('test_key');

        $ref       = new \ReflectionObject($item);
        $valueProp = $ref->getProperty('value');
        $valueProp->setAccessible(true);
        $hasValueProp = $ref->getProperty('hasValue');
        $hasValueProp->setAccessible(true);

        $this->assertEquals(null, $valueProp->getValue($item));
        $this->assertFalse($hasValueProp->getValue($item));

        $item->set('value');

        $this->assertEquals('value', $valueProp->getValue($item));
        $this->assertTrue($hasValueProp->getValue($item));
    }

    public function testGet()
    {
        $item = new CacheItem('test_key');
        $this->assertNull($item->get());

        $item->set('test');
        $this->assertEquals('test', $item->get());
    }

    public function testHit()
    {
        $item = new CacheItem('test_key');
        $this->assertFalse($item->isHit());

        $item->set('foobar');
        $this->assertTrue($item->isHit());

        $item->set(null);
        $this->assertTrue($item->isHit());

        $item->expiresAfter(1);
        $this->assertTrue($item->isHit());
        $item->expiresAfter(-1);
        $this->assertFalse($item->isHit());
    }

    public function testExpiresAt()
    {
        $item = new CacheItem('test_key');

        $ref  = new \ReflectionObject($item);
        $prop = $ref->getProperty('expirationDate');
        $prop->setAccessible(true);

        $this->assertNull($prop->getValue($item));
    }
}
