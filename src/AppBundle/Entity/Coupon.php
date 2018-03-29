<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Coupon
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CouponRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"store" = "StoreCoupon"})
 */
class Coupon
{
    const DEFAULT_POSITION = 10000;
    const DEFAULT_ACTIVITY = 1;
    const VERIVIED_TODAY = 'today';
    const VERIVIED_YESTERDAY = 'yesterday';
    const VERIVIED_DAYS_AGO = ' days ago';
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
     * @ORM\Column(name="label", type="text", nullable=false)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=20, nullable=true)
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     * @Assert\Length(max=20)
     */
    private $code;

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
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expireDate", type="date", nullable=true)
     */
    private $expireDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="activity", type="smallint", nullable=false)
     */
    private $activity = self::DEFAULT_ACTIVITY;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="smallint", nullable=false)
     * @Gedmo\SortablePosition
     * @Assert\NotNull
     */
    private $position = self::DEFAULT_POSITION;

    /**
     * @var CouponImage
     *
     * @ORM\OneToOne(targetEntity="CouponImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="logo", referencedColumnName="id", nullable=true)
     * @Assert\Valid
     **/
    private $logo;

    /**
     * @var Operator
     *
     * @ORM\OneToOne(targetEntity="Operator")
     * @ORM\JoinColumn(name="addedBy", referencedColumnName="id", nullable=true)
     *
     */
    private $addedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="verifiedAt", type="date", nullable=true)
     */
    private $verifiedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="subtype", type="string", nullable=true)
     */
    private $subtype;

    /**
     * @var string
     *
     * @ORM\Column(name="discount", type="string", nullable=true)
     */
    private $discount;

    /**
     * @var integer
     *
     * @ORM\Column(name="feed_id", type="integer", nullable=true)
     */
    private $feedId;

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
     * Set label
     *
     * @param string $label
     * @return Coupon
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Coupon
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Coupon
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
        $this->link = $this->transformData($this->link);
    }

    /**
     * Try to ransform given $data from stream into string and return this result. If failed return original $data.
     * @param resource $data
     *
     * @return mixed
     * @author Michael Strohyi
     **/
    public function transformData($data)
    {
        if (is_resource($data) && get_resource_type($data) == 'stream') {
            return stream_get_contents($data, -1, 0);
        }

        return $data;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Coupon
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set expireDate
     *
     * @param \DateTime $expireDate
     * @return Coupon
     */
    public function setExpireDate($expireDate)
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * Get expireDate
     *
     * @return \DateTime 
     */
    public function getExpireDate()
    {
        return $this->expireDate;
    }

    /**
     * Set activity
     *
     * @param integer $activity
     * @return Coupon
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
     * Set position
     *
     * @param integer $position
     * @return Coupon
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

    /**
     * Set logo
     *
     * @param CouponImage $logo
     * @return Coupon
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return CouponImage
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Remove logo
     *
     * @return Coupon
     */
    public function removeLogo()
    {
        $this->logo = null;

        return $this;
    }


    /**
     * Set verifiedAt
     *
     * @param \DateTime $verifiedAt
     * @return Coupon
     */
    public function setVerifiedAt($verifiedAt)
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }

    /**
     * Get verifiedAt
     *
     * @return \DateTime
     */
    public function getVerifiedAt()
    {
        return $this->verifiedAt;
    }

    /**
     * Set addedBy
     *
     * @param CouponImage $addedBy
     * @return Coupon
     */
    public function setAddedBy($addedBy)
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    /**
     * Get addedBy
     *
     * @return CouponImage
     */
    public function getAddedBy()
    {
        return $this->addedBy;
    }

    /**
     * Set subtype
     *
     * @param string $subtype
     * @return Coupon
     */
    public function setSubtype($subtype)
    {
        $this->subtype = $subtype;

        return $this;
    }

    /**
     * Get subtype
     *
     * @return string
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * Set discount
     *
     * @param string $discount
     * @return Coupon
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string 
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Return true if coupon is active and not expired
     *
     * @return boolean
     * @author Michael Strohyi
     **/
    public function isActual()
    {
        $current_date = date(time());
        $start_date = empty($this->startDate) ? null : $this->startDate->getTimestamp();
        $expire_date = empty($this->expireDate) ? null : $this->expireDate->getTimestamp();
        return $this->activity == 0 || !empty($start_date) && $start_date > $current_date || !empty($expire_date) && $expire_date < $current_date ? false : true;
    }

    /**
     * Get startDate for production in format yyyy-mm-dd
     *
     * @return string
     */
    public function getStartDateProd()
    {
        if (empty($this->startDate)) {
            return null;
        }

        return $this->startDate->format('Y-m-d');
    }

    /**
     * Get expireDate for production in format yyyy-mm-dd
     *
     * @return string
     */
    public function getExpireDateProd()
    {
        if (empty($this->expireDate)) {
            return null;
        }

        return $this->expireDate->format('Y-m-d');
    }

    /**
     * Set current date into UpdatedAt property
     *
     * @return self
     * @author Michael Strohyi
     **/
    public function setJustVerified()
    {
        $this->setVerifiedAt(new \DateTimeImmutable());

        return $this;
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
     * @return Coupon
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;

        return $this;
    }

    /**
     * Get encrypted code for production
     *
     * @return string
     * @author Michael Strohyi
     **/
    function getEncryptedCode()
    {
        $key = 'uspromocodes.com';
        $code = $this->getCode();
        if (empty($this->getCode()) || empty($key)) {
            return;
        }

        $code_len = strlen($code);
        $code_pos = 0;
        $key_len = strlen($key);
        $key_pos = -1;
        $chars_array = str_split($code);

        foreach($chars_array as &$char) {
            $char_code = ord($char);
            $key_pos = ++$key_pos < $key_len ? $key_pos : 0;
            $char_key = ord($key[$key_pos]);
            $char = chr($char_code ^ $char_key);
            $char = strlen(dechex(ord($char))) < 2 ? '\x0' . strtoupper(dechex(ord($char))) : '\x' . strtoupper(dechex(ord($char)));
        }

        return implode('', $chars_array);
    }

    /**
     * Return string with info when coupon was verified concerning today
     *
     * @return string|null
     * @author Michael Strohyi
     **/
    public function getWhenVerified()
    {
        $verifiedAt = $this->getVerifiedAt();
        if (empty($verifiedAt)) {
            return null;
        }

        $interval = date_diff($this->getVerifiedAt(), new \DateTimeImmutable())->format('%a');
        switch ($interval) {
            case '0':
                $interval = self::VERIVIED_TODAY;
                break;

            case '1':
                $interval = self::VERIVIED_YESTERDAY;
                break;

            default:
                $interval .= self::VERIVIED_DAYS_AGO;
                break;
        }

        return $interval;
    }

    /**
     * Find maximum discount in coupon's label and set it into discount property
     *
     * @return self
     * @author Michael Strohyi
     **/
    public function setMaxDiscount()
    {
        $this->setDiscount(self::findMaxDiscount($this->getLabel()));

        return $this;
    }

    /**
     * Find maximum discount in given label
     *
     * @param string $label
     * @return string
     * @author Michael Strohyi
     **/
    public static function findMaxDiscount($label)
    {
        preg_match_all('/[0-9]+%/', $label, $matches);
        if (!empty($matches[0])) {
            return(max($matches[0]));
        }

        $pattern = '/free shipping/i';
        if (preg_match('/free shipping/i', $label) != 0) {
            return 'FREE Shipping';
        }

        return null;
    }
}
