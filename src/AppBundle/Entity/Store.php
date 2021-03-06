<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;
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
    const DEFAULT_USE_FEED_LINKS = false;
    const RANDOM_DISCOUNTS = [10, 15, 20, 25, 30];
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
     * @var string
     *
     * @ORM\Column(name="meta_keywords", type="text", nullable=true)
     */
    private $metaKeywords;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    private $metaDescription;


    /**
     * @var string
     *
     * @ORM\Column(name="metatags", type="text", nullable=true)
     * @AppAssert\ValidHTML
     */
    private $metatags;

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

    /**
     * @var boolean
     *
     * @ORM\Column(name="use_feed_links", type="boolean", nullable=false, options={"default"=false})
     */
    private $useFeedLinks = self::DEFAULT_USE_FEED_LINKS;

    /**
     * @var boolean
     *
     * @ORM\Column(name="network_error", type="boolean", nullable=false, options={"default"=false})
     */
    private $networkError = false;

    /**
     * @var Comments[]
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="store", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"addedDate" = "asc"})
     * @Assert\Valid
     **/
    private $comments;

    /**
     * @var string
     *
     * @ORM\Column(name="how_to_use", type="text", nullable=true)
     */
    private $howToUse;

    /**
     * @var string
     *
     * @ORM\Column(name="news", type="text", nullable=true)
     */
    private $news;

    public function __construct()
    {
        $this->coupons = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Store
     */
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
     * Set metaKeywords
     *
     * @param string $metaKeywords
     * @return Store
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string 
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     * @return Store
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string 
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Return url of store's page, based on store name
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->makeUrlFromName($this->name);
    }

    /**
     * Return url of stores's page, based on given store's name
     *
     * @return string
     */
    public function makeUrlFromName($name)
    {
        return '/' . $this->convertNameToUrl($name) . self::URL_POSTFIX;
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
     * Add comment
     *
     * @param Comment $comment
     * @return Store
     */
    public function addComment(Comment $comment)
    {
        $comment->setStore($this);
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param Comment $comments
     */
    public function removeComment(Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
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
     * @return boolean
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set useFeedLinks
     *
     * @param integer $useFeedLinks
     * @return Store
     */
    public function setUseFeedLinks($useFeedLinks)
    {
        $this->useFeedLinks = $useFeedLinks;

        return $this;
    }

    /**
     * Get useFeedLinks
     *
     * @return boolean
     */
    public function getUseFeedLinks()
    {
        return $this->useFeedLinks;
    }

    /**
     * Set networkError
     *
     * @param integer $networkError
     * @return Store
     */
    public function setNetworkError($networkError)
    {
        $this->networkError = $networkError;

        return $this;
    }

    /**
     * Get networkError
     *
     * @return boolean
     */
    public function getNetworkError()
    {
        return $this->networkError;
    }

    /**
     * Set metatags
     *
     * @param string $metatags
     * @return Store
     */
    public function setMetatags($metatags)
    {
        $this->metatags = $metatags;

        return $this;
    }

    /**
     * Get metatags
     *
     * @return string
     */
    public function getMetatags()
    {
        return $this->metatags;
    }

    /**
     * Set howToUse
     *
     * @param string $howToUse
     * @return Store
     */
    public function setHowToUse($howToUse)
    {
        $this->howToUse = $howToUse;

        return $this;
    }

    /**
     * Get howToUse
     *
     * @return string
     */
    public function getHowToUse()
    {
        return $this->howToUse;
    }

    /**
     * Set news
     *
     * @param string $news
     * @return Store
     */
    public function setNews($news)
    {
        $this->news = $news;

        return $this;
    }

    /**
     * Get news
     *
     * @return string
     */
    public function getNews()
    {
        return $this->news;
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
     * Return first 40 words of description
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
     * Return rest of description (after first 40 words)
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
     * Set new actual position for all coupons
     *
     * @param int $limit
     * @return void
     * @author Michael Strohyi
     **/
    public function actualiseCouponsPosition()
    {
        $coupons = $this->getCoupons();
        $pos = 0;
        foreach ($coupons as $coupon) {
           $coupon->setPosition($pos++);
        }
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
     * Return discount of first actual coupon if discount is like xx%.
     * If first coupon has no discount like xx% return null or random discount, if $random_enable param is true
     *
     * @return string
     * @author Michael Strohyi
     **/
    public function getMainDiscount($random_enable = false)
    {
        $coupons = $this->getCoupons();
        foreach ($coupons->getIterator() as $coupon) {
            if (!$coupon->isActual()){
                continue;
            }

            if (strpos($coupon->getDiscount(), '%') !== false) {
                return $coupon->getDiscount();
            }

            break;
        }

        return $random_enable ? self::RANDOM_DISCOUNTS[count($coupons) % count(self::RANDOM_DISCOUNTS)] . '%' : null;
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

    /**
     * Return metaKeywords in format of one string. Keywords are separated by ', '
     *
     * @return string
     * @author Michael Strohyi
     **/
    public function getMetaKeywordsString()
    {
        return self::makeStringFromText($this->getMetaKeywords());
    }
    /**
     * Return metaDescription in format of one string. Keywords are separated by '. '
     *
     * @return string
     * @author Michael Strohyi
     **/
    public function getMetaDescriptionString()
    {
        return self::makeStringFromText($this->getMetaDescription(), '.');
    }

    /**
     * Return given text of one string. Lines are separated by given delimeter
     *
     * @param string $text
     * @param string $delimeter
     * @return string
     * @author Michael Strohyi
     **/
    public static function makeStringFromText($text, $delimeter = ',')
    {
        $text_array = array_filter(explode(PHP_EOL, $text));
        # if delimeter is '.' capitalize words after delimeter
        if ($delimeter == '.') {
            foreach ($text_array as $key => $value) {
                if ($key == 0) {
                    continue;
                }
                $text_array[$key] = ucfirst($value);
            }
        }

        return implode($delimeter . ' ', $text_array);
    }

    /**
     * Clear LastUpdate field for coupons with not null feedId
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function clearLastUpdatedFeedCoupons()
    {
        $coupons = $this->getCoupons();
        foreach ($coupons as $coupon) {
            if (!empty($coupon->getFeedId())) {
                $coupon->setLastUpdated(null);
            }
        }
    }

    /**
     * Set store link for all coupons with not null feedId
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function resetLinkForAllFeedCoupons()
    {
        $coupons = $this->getCoupons();
        $link = $this->getLink();
        foreach ($coupons as $coupon) {
            if (!empty($coupon->getFeedId())) {
                $coupon->setLink($link);
            }
        }
    }
}
