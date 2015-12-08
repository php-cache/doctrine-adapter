<?php

/*
 * This file is part of php-cache\doctrine-adapter package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\Doctrine;

use Cache\Doctrine\Exception\InvalidArgumentException;
use Cache\Taggable\TaggableItemInterface;
use Cache\Taggable\TaggablePoolInterface;
use Cache\Taggable\TaggablePoolTrait;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FlushableCache;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * This is a bridge between PSR-6 and aDoctrine cache.
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class CachePool implements CacheItemPoolInterface, TaggablePoolInterface
{
    use TaggablePoolTrait;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var CacheItemInterface[] deferred
     */
    private $deferred = [];

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key, array $tags = array())
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Passed key is invalid');
        }

        $taggedKey = $this->generateCacheKey($key, $tags);

        $item = $this->cache->fetch($taggedKey);
        if (false === $item || !$item instanceof CacheItemInterface) {
            $item = new CacheItem($taggedKey, $tags);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [], array $tags = array())
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key, $tags);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key, array $tags = array())
    {
        return $this->getItem($key, $tags)->isHit();
    }

    /**
     * {@inheritdoc}
     */
    public function clear(array $tags = array())
    {
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $this->flushTag($tag);
            }

            return true;
        }

        if ($this->cache instanceof FlushableCache) {
            return $this->cache->flushAll();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key, array $tags = array())
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Passed key is invalid');
        }
        $taggedKey = $this->generateCacheKey($key, $tags);

        return $this->cache->delete($taggedKey);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys, array $tags = array())
    {
        $deleted = true;
        foreach ($keys as $key) {
            if (!$this->deleteItem($key, $tags)) {
                $deleted = false;
            }
        }

        return $deleted;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        $timeToLive = 0;
        if ($item instanceof HasExpirationDateInterface) {
            if (null !== $expirationDate = $item->getExpirationDate()) {
                $timeToLive = $expirationDate->getTimestamp() - time();
            }
        }

        $tags = array();
        if  ($item instanceof TaggableItemInterface) {
            $tags = $item->getTags();
        }
        $taggedKey = $this->generateCacheKey($item->getKey(), $tags);

        return $this->cache->save($taggedKey, $item, $timeToLive);
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[] = $item;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $saved = true;
        foreach ($this->deferred as $item) {
            if (!$this->save($item)) {
                $saved = false;
            }
        }
        $this->deferred = [];

        return $saved;
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }
}
