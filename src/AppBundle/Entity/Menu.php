<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Menu
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MenuRepository")
 * @UniqueEntity("name", message="Menu with this name already exists")
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
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=30)
     * @Assert\Regex(pattern="/^[a-zA-Z\d_\-]+$/", message="You can use only alphabet and dashes")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="string", length=255, nullable=false)
     * @Assert\Length(max=200)
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     */
    private $header;

    /**
     * @var MenuItem[]
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="menu")
     * @ORM\OrderBy({"position" = "asc"})
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
     * Add item
     *
     * @param MenuItem $item
     * @return Menu
     */
    public function addItem(MenuItem $item)
    {
        $item->setMenu($this);
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param MenuItem $item
     */
    public function removeItem(MenuItem $item)
    {
        $this->items->removeElement($item);
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
