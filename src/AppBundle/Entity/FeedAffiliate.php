<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * FeedAffiliate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FeedAffiliateRepository")
 * @UniqueEntity("network", message="Replacement for this network already exists")
 * @ORM\HasLifecycleCallbacks
 */
class FeedAffiliate
{
    const DEFAULT_FEED_AFFILIATE_ID = 'ACTID';

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
     * @ORM\Column(name="network", type="string", length=30, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=30)
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     */
    private $network;

    /**
     * @var string
     *
     * @ORM\Column(name="affiliate_id", type="blob", nullable=false)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     */
    private $affiliate_id;

    /**
     * @var string
     *
     * @ORM\Column(name="feed_affiliate_id", type="blob", nullable=false)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     */
    private $feed_affiliate_id = self::DEFAULT_FEED_AFFILIATE_ID;



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
     * Set network
     *
     * @param string $network
     * @return FeedAffiliate
     */
    public function setNetwork($network)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Get network
     *
     * @return string 
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Set affiliate_id
     *
     * @param string $affiliate_id
     * @return FeedAffiliate
     */
    public function setAffiliateId($affiliate_id)
    {
        $this->affiliate_id = $affiliate_id;

        return $this;
    }

    /**
     * Get affiliate_id
     *
     * @return string 
     */
    public function getAffiliateId()
    {
        return $this->affiliate_id;
    }

    /**
     * Set feed_affiliate_id
     *
     * @param string $feed_affiliate_id
     * @return FeedAffiliate
     */
    public function setFeedAffiliateId($feed_affiliate_id)
    {
        $this->feed_affiliate_id = $feed_affiliate_id;

        return $this;
    }

    /**
     * Get feed_affiliate_id
     *
     * @return string 
     */
    public function getFeedAffiliateId()
    {
        return $this->feed_affiliate_id;
    }

    /**
     * @ORM\PostLoad
     *
     * @return void
     * @author Michael Strohyi
     */
    public function transformLoadedData()
    {
        if (is_resource($this->affiliate_id) && get_resource_type($this->affiliate_id) == 'stream') {
            $this->affiliate_id = stream_get_contents($this->affiliate_id, -1, 0);
        }

        if (is_resource($this->feed_affiliate_id) && get_resource_type($this->feed_affiliate_id) == 'stream') {
            $this->feed_affiliate_id = stream_get_contents($this->feed_affiliate_id, -1, 0);
        }
    }
}
