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
    const URL_POSTFIX = '/coupons'; // WARNING! Change postfix only with changing urls for store pages in page db
    const DEFAULT_ACTIVITY = true;
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

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_featured", type="boolean", nullable=false, options={"default"=false})
     **/
    private $is_featured;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activity", type="boolean", nullable=false, options={"default"=true})
     */
    private $activity = self::DEFAULT_ACTIVITY;

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
        return '/' . $this->convertNameToUrl($this->name) . self::URL_POSTFIX;
    }

    /**
     * Set is_featured
     *
     * @param boolean $is_featured
     * @return Store
     */
    public function setIsFeatured($is_featured)
    {
        $this->is_featured = $is_featured;

        return $this;
    }

    /**
     * Get is_featured
     *
     * @return boolean
     */
    public function getIsFeatured()
    {
        return $this->is_featured;
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
     * Set activity
     *
     * @param integer $activity
     * @return Store
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return integer
     */
    public function getActivity()
    {
        return $this->activity;
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
     * @param int $limit
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
        # if max position is 0 return it
        if ($max == 0) {
            return 0;
        }

        $coupons = $this->getCoupons()->toArray();
        $coupons_count = count($coupons);
        # return 0 if there are no coupons in this store
        if ($coupons_count == 0) {
            return 0;
        }

        $pos = -1;
        # go through coupons array for this store
        foreach ($coupons as $coupon) {
            # calculate position of current coupon
            $pos++;
            # break if current pos is equal to max
            if ($pos == $max) {
                break;
            }
            # continue if current coupon is not feed-coupon or pos is lower, than min position
            if ($pos < $min || empty($coupon->getFeedId())) {
                continue;
            }
            # return current pos if rating of current coupon is not higher, than given rating
            if ($coupon->getRating() <= $rating) {
                return $pos;
            }
        }
        # return max if position was not found before
        return $max;
    }

    /**
     * Return const PAGE_TYPE
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function getType()
    {
        return self::PAGE_TYPE;
    }

    /**
     * Return discount of first actual coupon if discount is like xx%
     *
     * @return string
     * @author Michael Strohyi
     **/
    public function getMainDiscount()
    {
        foreach ($this->getCoupons()->getIterator() as $coupon) {
            if (!$coupon->isActual()){
                continue;
            }

            return strpos($coupon->getDiscount(), '%') !== false ? $coupon->getDiscount() : null;
        }

        return null;
    }

    /**
     * Return count of actual coupons
     *
     * @return integer
     * @author Michael Strohyi
     **/
    public function getCouponsCount()
    {
        $res = 0;
        foreach ($this->getCoupons()->getIterator() as $coupon) {
            if ($coupon->isActual()){
                $res++;
            }
        }

        return $res;
    }

    /**
     * Return count of coupons, added by operators manually
     *
     * @return integer
     * @author Michael Strohyi
     **/
    public function getManualCouponsCount()
    {
        $res = 0;
        foreach ($this->getCoupons()->getIterator() as $coupon) {
            if (empty($coupon->getFeedId())) {
                $res++;
            }
        }

        return $res;
    }
}
