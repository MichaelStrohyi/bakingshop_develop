<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Redirect
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RedirectRepository")
 * @UniqueEntity("url", message="Redirect for this url already exists")
 * @ORM\HasLifecycleCallbacks
 */
class Redirect
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
}
