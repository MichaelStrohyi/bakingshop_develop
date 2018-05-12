<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
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
    const PAGE_TYPE = 'menu';
    const TYPE_TOP = 'top';
    const TYPE_SIDE = 'side';
    const TYPE_BOTTOM = 'bottom';
    const PROD_COLORS = ['red', 'blue', 'yellow'];
    const DEFAULT_POSITION = 10000;

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
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=200)
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     */
    private $header;

    /**
     * @var MenuItem[]
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="menu", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "asc"})
     * @Assert\Valid
     **/
    private $items;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=false, options={"default"=Menu::TYPE_SIDE})
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @var integer
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=false)
     * @Assert\NotNull
     */
    private $position = self::DEFAULT_POSITION;


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

    /**
     * Sort items by position
     *
     * @return self
     * 
     * @author Mykola Martynov
     **/
    public function sortItems()
    {
        $items = [];
        foreach ($this->items as $item) {
            $key = $item->getPosition();
            $items[$key] = $item;
        }
        ksort($items);
        $this->items = new ArrayCollection($items);
        
        return $this;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Menu
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get list of available types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_TOP,
            self::TYPE_SIDE,
            self::TYPE_BOTTOM,
        ];
    }

    /**
     * Set position
     *
     * @param int $position
     * @return Menu
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
