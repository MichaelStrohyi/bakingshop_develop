<?php

namespace USPC\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Page
 *
 * @ORM\Table(indexes={
 *     @Index(name="search_url", columns={"url"}),
 *     @Index(name="search_type_url", columns={"type", "url"}),
 *     @Index(name="search_type_object", columns={"type", "object_id"})
 * })
 * @ORM\Entity(repositoryClass="USPC\PageBundle\Entity\PageRepository")
 */
class Page
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
     * @ORM\Column("url", type="blob", length=100)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column("type", type="string", length=20)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column("object_id", type="integer")
     */
    private $object_id;

    /**
     * @var boolean
     *
     * @ORM\Column("is_alias", type="boolean")
     */
    private $is_alias;

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
     * Set url
     *
     * @param string $url
     *
     * @return Page
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @ORM\PostLoad
     *
     * @return void
     * @author Mykola Martynov
     **/
    public function transformLoadedData()
    {
        if (is_resource($this->url) && get_resource_type($this->url) == 'stream') {
            $this->url = stream_get_contents($this->url, -1, 0);
        }
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Page
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
     * Set objectId
     *
     * @param integer $objectId
     *
     * @return Page
     */
    public function setObjectId($objectId)
    {
        $this->object_id = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId()
    {
        return $this->object_id;
    }

    /**
     * Set isAlias
     *
     * @param boolean $isAlias
     *
     * @return Page
     */
    public function setIsAlias($isAlias)
    {
        $this->is_alias = $isAlias;

        return $this;
    }

    /**
     * Get isAlias
     *
     * @return boolean
     */
    public function getIsAlias()
    {
        return $this->is_alias;
    }

    /**
     * Get isAlias
     *
     * @return boolean
     * @author Mykola Martynov
     **/
    public function isAlias()
    {
        return $this->getIsAlias();
    }
}
