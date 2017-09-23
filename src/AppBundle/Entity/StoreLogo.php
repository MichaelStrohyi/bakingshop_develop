<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StoreLogo
 *
 * @ORM\Entity
 */
class StoreLogo extends Image
{
    /**
     * @var Store
     *
     **/
    private $store;

    /**
     * Set store
     *
     * @param Store $store
     * @return StoreLogo
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
