<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Store
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\StoreRepository")
 * @UniqueEntity("name", message="Store with this name already exists")
 * @ORM\HasLifecycleCallbacks
 */
class Store
{
    const PAGE_TYPE = 'store';
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
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=40)
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="blob", nullable=false)
     * @Assert\NotBlank
     * @Assert\Url(
     *    message = "The link '{{ value }}' is not a valid url",
     * )
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="text", nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var StoreCoupon[]
     *
     * @ORM\OneToMany(targetEntity="StoreCoupon", mappedBy="store", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "asc"})
     * @Assert\Valid
     **/
    private $coupons;

    /**
     * @var StoreLogo
     *
     * @ORM\OneToOne(targetEntity="StoreLogo", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="logo", referencedColumnName="id", nullable=true)
     * @Assert\Valid
     **/
    private $logo;

    public function __construct()
    {
        $this->coupons = new ArrayCollection();
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
     * @return Store
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
     * Set link
     *
     * @param string $link
     * @return Store
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Store
     */

    /**
     * @ORM\PostLoad
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function transformLoadedData()
    {
        if (is_resource($this->link) && get_resource_type($this->link) == 'stream') {
            $this->link = stream_get_contents($this->link, -1, 0);
        }
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Store
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return page name for local url, based on store name
     *
     * @return string
     */
    public function getUrl()
    {
        return '/' . $this->convertNameToUrl($this->name);
    }

    /**
     * Add coupon
     *
     * @param StoreCoupon $coupon
     * @return Store
     */
    public function addCoupon(StoreCoupon $coupon)
    {
        $coupon->setStore($this);
        $this->coupons[] = $coupon;

        return $this;
    }

    /**
     * Remove coupons
     *
     * @param StoreCoupon $coupons
     */
    public function removeCoupon(StoreCoupon $coupons)
    {
        $this->coupons->removeElement($coupons);
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * Set logo
     *
     * @param StoreLogo $logo
     * @return Store
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return StoreLogo 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Remove logo
     *
     * @return Store
     */
    public function removeLogo()
    {
        $this->logo = null;

        return $this;
    }

    /**
     * Return page name for local url, based on store name
     *
     * @param string $name
     * @return string
     */
    public function convertNameToUrl($name = null)
    {
        if (empty($name)) {
            $name = $this->getName();
        }

        $pattern = [
            "/&/",
            "/['\"`()\[\]{}]/",
            "/[\s]+/",
            "/(?(?=[[:punct:]])[^.!:]|^$)+/",
        ];
        $replacement = [
            "-and-",
            "",
            "-",
            "-",
        ];
        return strtolower(trim(preg_replace($pattern, $replacement, $name), '-'));
    }

    /**
     * Return first 30 words of description
     *
     * @return string
     */
    public function getShortDescription()
    {
        $descr_array = explode(' ', $this->getDescription());
        if (count($descr_array) > 40) {
            return implode(' ', array_slice($descr_array, 0, 40));
        }

        return $this->getDescription();
    }

    /**
     * Return rest of description (after first 30 words)
     *
     * @return string
     */
    public function getRestDescription()
    {
        $descr_array = explode(' ', $this->getDescription());
        if (count($descr_array) > 40) {
            return implode(' ', array_slice($descr_array, 40));
        }

        return null;
    }
}
