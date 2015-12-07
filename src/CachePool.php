<?php

namespace Cache\Doctrine;

use Cache\Doctrine\Exception\InvalidArgumentException;
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
class CachePool implements CacheItemPoolInterface
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var CacheItemInterface[] deferred
     */
    private $deferred;

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
    public function getItem($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Passed key is invalid');
        }

        /** @var CacheItemInterface $item */
        $item = $this->cache->fetch($key);
        if (false === $item || !$item instanceof CacheItemInterface) {
            $item = new CacheItem($key);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        return $this->getItem($key)->isHit();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        if ($this->cache instanceof FlushableCache) {
            return $this->cache->flushAll();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Passed key is invalid');
        }

        return $this->cache->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        $deleted = true;
        foreach ($keys as $key) {
            if (!$this->deleteItem($key)) {
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
        if (!$item instanceof HasExpirationDateInterface) {
            throw new InvalidArgumentException(
                'Item passed must be an instance of Cache\Doctrine\HasExpirationDateInterface'
            );
        }

        return $this->cache->save($item->getKey(), $item, $item->getExpirationDate()->getTimestamp() - time());
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
        foreach ($this->deferred as $key => $item) {
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
