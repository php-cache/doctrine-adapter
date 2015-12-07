<?php

namespace Cache\Doctrine;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface CacheItemInterface extends \Psr\Cache\CacheItemInterface
{
    /**
     * @return bool
     */
    public function isExpired();

    /**
     * @return \DateTime|null
     */
    public function getExpirationDate();
}
