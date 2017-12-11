<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\AMP\AppAMP;

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
    const PAGE_SUBTYPE_ARTICLE = 'article';
    const PAGE_SUBTYPE_RECIPE = 'recipe';
    const PAGE_SUBTYPE_INFO = 'info';

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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=false)
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     * @Assert\Length(min=3, max=255)
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="amp_body", type="text", nullable=true)
     */
    private $ampBody;

    /**
     * @var ArticleLogo
     *
     * @ORM\OneToOne(targetEntity="ArticleLogo", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="logo", referencedColumnName="id", nullable=true)
     * @Assert\Valid
     **/
    private $logo;


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
        $this->updateAmpBody();

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

    /**
     * Set type
     *
     * @param string $type
     * @return Article
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
            self::PAGE_SUBTYPE_ARTICLE,
            self::PAGE_SUBTYPE_RECIPE,
            self::PAGE_SUBTYPE_INFO,
        ];
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Article
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set ampBody
     *
     * @param string $ampBody
     * @return Article
     */
    public function setAmpBody($ampBody)
    {
        $this->ampBody = $ampBody;

        return $this;
    }

    /**
     * Get ampBody
     *
     * @return string 
     */
    public function getAmpBody()
    {
        return $this->ampBody;
    }

    /**
     * Actualize ampBody with current body
     *
     * @return Article
     */
    private function updateAmpBody()
    {
        if (is_null($this->getBody())) {
            $this->ampBody = null;
            return;
        }

        $amp = new AppAMP();
        $amp->loadHtml($this->getBody(), [
            'img_max_fixed_layout_width' => '100'
            ]);
        $this->ampBody = $amp->convertToAmpHtml();

        return $this;
    }

    /**
     * Set logo
     *
     * @param ArticleLogo $logo
     * @return Article
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return ArticleLogo 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Remove logo
     *
     * @return Article
     */
    public function removeLogo()
    {
        $this->logo = null;

        return $this;
    }
}
