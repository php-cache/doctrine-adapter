<?php

namespace Cache\Doctrine;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface HasExpirationDateInterface
{
    /**
     * @return \DateTime|null
     */
    public function getExpirationDate();
}
