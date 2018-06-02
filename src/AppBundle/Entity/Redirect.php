<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Redirect
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RedirectRepository")
 * @UniqueEntity("prodUrl", message="Redirect for this url already exists")
 * @UniqueEntity("url", message="Redirect to this url already exists")
 * @ORM\HasLifecycleCallbacks
 */
class Redirect
{
    const DEFAULT_POSITION = 10000;
    const PAGE_TYPE = 'redirect';

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
     * @ORM\Column(name="url", type="blob")
     * @AppAssert\LocalURL
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="prodUrl", type="blob")
     * @AppAssert\LocalURL
     */
    private $prodUrl;

    /**
     * @var integer
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=false)
     * @Assert\NotNull
     */
    private $position = self::DEFAULT_POSITION;



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
     * @return Redirect
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
     * Set prodUrl
     *
     * @param string $prodUrl
     * @return Redirect
     */
    public function setProdUrl($prodUrl)
    {
        $this->prodUrl = $prodUrl;

        return $this;
    }

    /**
     * Get prodUrl
     *
     * @return string 
     */
    public function getProdUrl()
    {
        return $this->prodUrl;
    }

    /**
     * @ORM\PostLoad
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function transformLoadedData()
    {
        if (is_resource($this->url) && get_resource_type($this->url) == 'stream') {
            $this->url = stream_get_contents($this->url, -1, 0);
        }

        if (is_resource($this->prodUrl) && get_resource_type($this->prodUrl) == 'stream') {
            $this->prodUrl = stream_get_contents($this->prodUrl, -1, 0);
        }
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Redirect
     */
    public function setPosition($position)
    {
        $this->position = intval($position);

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

}
