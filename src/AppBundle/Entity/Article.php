<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ArticleRepository")
 * @UniqueEntity("header", message="Article with this header already exists")
 * @ORM\HasLifecycleCallbacks
 */
class Article
{
    const PAGE_TYPE = 'article';

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
     * @ORM\Column(name="header", type="string", length=255, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=255)
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     */
    private $header;

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
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_homepage", type="boolean", nullable=false, options={"default"=false})
     **/
    private $is_homepage;


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
     * Set header
     *
     * @param string $header
     * @return Article
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
     * Set url
     *
     * @param string $url
     * @return Article
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
     * @author Michael Strohyi
     **/
    public function transformLoadedData()
    {
        if (is_resource($this->url) && get_resource_type($this->url) == 'stream') {
            $this->url = stream_get_contents($this->url, -1, 0);
        }
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Article
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return true if current article is set as homepage, otherwise return false.
     * Set value if $isHomepage is not null.
     * 
     * @param boolean $isHomepage
     * 
     * @return boolean|self
     */
    public function isHomepage($isHomepage = null)
    {
        if (is_null($isHomepage)) {
            return $this->is_homepage;
        }

        $this->is_homepage = !empty($isHomepage);

        return $this;
    }

    /**
     * Set is_homepage
     *
     * @param boolean $isHomepage
     * @return Article
     */
    public function setIsHomepage($isHomepage)
    {
        $this->is_homepage = $isHomepage;

        return $this;
    }

    /**
     * Get is_homepage
     *
     * @return boolean 
     */
    public function getIsHomepage()
    {
        return $this->is_homepage;
    }
}
