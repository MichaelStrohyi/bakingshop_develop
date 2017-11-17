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
    const SUBTYPE_INSTORE = 'in_store';

    /**
     * @var Store
     *
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="coupons")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="id")
     **/
    private $store;

    /**
     * @var boolean
     *
     **/
    private $inStore;

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

    /**
     * Set inStore
     *
     * @param boolean $inStore
     * @return StoreCoupon
     */
    public function setInStore($inStore)
    {
        $this->inStore = $inStore;
        $inStore == true ? $this->setSubtype(self::SUBTYPE_INSTORE) : $this->setSubtype(null);

        return $this;
    }

    /**
     * Get inStore
     *
     * @return boolean
     */
    public function getInStore()
    {
        return ($this->getSubtype() == self::SUBTYPE_INSTORE) ? true : false;
    }
}
