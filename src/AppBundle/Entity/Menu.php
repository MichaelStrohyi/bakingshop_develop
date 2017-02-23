<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Menu
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MenuRepository")
 */
class Menu
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="string", length=255)
     */
    private $header;

    /**
     * @var MenuItem[]
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="menu")
     **/
    private $items;


    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set header
     *
     * @param string $header
     * @return Menu
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return string 
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Add items
     *
     * @param MenuItem $items
     * @return Menu
     */
    public function addItem(MenuItem $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param MenuItem $items
     */
    public function removeItem(MenuItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return ArrayCollection 
     */
    public function getItems()
    {
        return $this->items;
    }
}
