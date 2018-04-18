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
 * @UniqueEntity("feedId", message="This id is already set for other store")
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

    /**
     * @var integer
     *
     * @ORM\Column(name="feed_id", type="integer", nullable=true)
     */
    private $feedId;

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
     * Set coupons
     *
     * @return self
     */
    public function setCoupons($coupons)
    {
        $this->coupons = $coupons;

        return $this;
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
     * Get feedId
     *
     * @return integer
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * Set feedId
     *
     * @param integer $label
     * @return Store
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;

        return $this;
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
    }

    /**
     * Search for coupon with given code and feedId different from given feedId. Return target coupon if it exists or null, if it does not exist
     *
     * @param string $code
     * @param int $feedId
     *
     * @return StoreCoupon|null
     * @author Michael Strohyi
     **/
    public function findCouponByCode($code, $feedId = 0)
    {
        if (empty($code)) {
            return;
        }

        $coupons = $this->getCoupons();
        foreach($coupons->getIterator() as $coupon) {
            $exists = strtolower($coupon->getCode()) == strtolower($code) && $coupon->getFeedId() !== $feedId ? true : false;
            if ($exists) {
                return $coupon;
            }
        }
    }

    /**
     * Return position of last coupon with code in coupons array. If no coupons have code return -1
     *
     * @return int
     * @author Michael Strohyi
     **/
    public function getLastCodePosition()
    {
        $coupons = $this->getCoupons();
        $last_code = -1;
        $pos = -1;
        foreach($coupons->getIterator() as $coupon) {
            $pos++;
            if (!empty($coupon->getCode())) {
                $last_code = $pos;
            }
        }

        return $last_code;
    }

    /**
     * Insert coupon into coupons on given position
     *
     * @param StoreCoupon $coupon
     * @param int $position
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function insertCouponOnPosition($coupon, $position)
    {
        $coupons = $this->getCoupons()->toArray();
        array_splice($coupons, $position, 0, [$coupon]);
        $this->setCoupons(new ArrayCollection($coupons));
    }

    /**
     * Set new actual position for all coupons, remove coupons with position > given limit
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function actualiseCouponsPosition($limit = 0)
    {
        $coupons = $this->getCoupons()->toArray();
        $pos = 0;
        foreach ($coupons as $coupon) {
           if ($limit > 0 && $pos >= $limit) {
            $this->removeCoupon($coupon);
            continue;
           }

           $coupon->setPosition($pos++);
        }

        $this->setCoupons(new ArrayCollection($coupons));
    }


    /**
     * Search for coupon with given feedId. Return target coupon or null, if feedId does not exist
     *
     * @param string $feedId
     *
     * @return StoreCoupon|null
     * @author Michael Strohyi
     **/
    public function findCouponByFeedId($feedId)
    {
        if (empty($feedId)) {
            return;
        }

        $coupons = $this->getCoupons();
        foreach($coupons->getIterator() as $coupon) {
            $exists = $coupon->getFeedId() == $feedId ? true : false;
            if ($exists) {
                return $coupon;
            }
        }
    }

    /**
     * Remove coupons, which have not null FeedId and are absent in given $exclusions.
     * Return true if any coupon has been removed, otherwise return false.
     *
     * @param array $exclusions
     *
     * @return boolean
     * @author Michael Strohyi
     **/
    public function removeFeedCoupons($exclusions = [])
    {
        $res = false;
        foreach($this->getCoupons()->getIterator() as $coupon) {
            if (!empty($coupon->getFeedId()) && !in_array($coupon->getFeedId(), $exclusions)) {
                $this->removeCoupon($coupon);
                $res = true;
            }
        }

        return $res;
    }

    /**
     * Find position for coupon by given rating and possible range of positions
     *
     * @param float $rating
     * @param int $max
     * @param int $min
     *
     * @return int
     * @author Michael Strohyi
     **/
    public function findCouponPositionByRating($rating, $max = 0, $min = 0)
    {
        $coupons = $this->getCoupons()->toArray();
        $coupons_count = count($coupons);
        if ($coupons_count == 0) {
            return 0;
        }

        if ($max == 0) {
            $max = $coupons_count;
        }

        $pos = -1;
        foreach ($coupons as $coupon) {
            $pos++;
            if ($pos == $max) {
                break;
            }

            if ($pos < $min || empty($coupon->getFeedId())) {
                continue;
            }

            if ($coupon->getRating() <= $rating) {
                return $pos;
            }
        }

        return $max;
    }

}
