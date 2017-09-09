<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StoreCoupon
 *
 * @ORM\Entity
 */
class StoreCoupon extends Coupon
{
    /**
     * @var Store
     *
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="coupons")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="id")
     **/
    private $store;
    /**
     * Set store
     *
     * @param Store $store
     * @return StoreCoupon
     */
    public function setStore(Store $store = null)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get store
     *
     * @return Store 
     */
    public function getStore()
    {
        return $this->store;
    }
}
