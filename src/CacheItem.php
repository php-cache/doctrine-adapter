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
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class CacheItem implements HasExpirationDateInterface, CacheItemInterface, TaggableItemInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var \DateTime|null
     */
    private $expirationDate = null;

    /**
     * @var bool
     */
    private $hasValue = false;

    /**
     * @var array
     */
    private $tags = array();

    /**
     * @param string $key
     */
    public function __construct($key, array $tags = array())
    {
        $this->key  = $key;
        $this->tags = $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value)
    {
        $this->value    = $value;
        $this->hasValue = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit()
    {
        if (!$this->hasValue) {
            return false;
        }

        if ($this->expirationDate === null) {
            return true;
        }

        return ((new \DateTime()) <= $this->expirationDate);
    }

    /**
     * @return \DateTime|null
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expiration)
    {
        $this->expirationDate = $expiration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time)
    {
        if ($time === null) {
            $this->expirationDate = null;
        } elseif ($time instanceof \DateInterval) {
            $this->expirationDate = new \DateTime();
            $this->expirationDate->add($time);
        } else {
            $this->expirationDate = new \DateTime(sprintf('+%sseconds', $time));
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }
}
