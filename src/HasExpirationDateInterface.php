<?php

namespace Cache\Doctrine;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface HasExpirationDateInterface
{
    /**
     * The date and time when the object expires.
     *
     * @return \DateTime|null
     */
    public function getExpirationDate();
}
