<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CommentRepository")
 */
class Comment
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
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="text", nullable=false)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]£€]*$/")
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=60, nullable=false)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     * @Assert\Length(max=60)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=80, nullable=false)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[\w\d\s[:punct:]]*$/")
     * @Assert\Length(max=60)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_verified", type="boolean", nullable=false, options={"default"=false})
     */
    private $isVerified = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="added_date", type="datetime", nullable=false)
     */
    private $addedDate;

    /**
     * @var Store
     *
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="comments")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="id")
     **/
    private $store;

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
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return Comment
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }
    /**
     * Set label
     *
     * @param string $label
     * @return Comment
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
     * Set author
     *
     * @param string $author
     * @return Comment
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
     * Set email
     *
     * @param string $email
     * @return Comment
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isVerified
     *
     * @param integer $isVerified
     * @return Comment
     */
    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Get isVerified
     *
     * @return boolean
     */
    public function getIsVerified()
    {
        return $this->isVerified;
    }

    /**
     * Set addedDate
     *
     * @param \DateTime $addedDate
     * @return Comment
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get addedDate
     *
     * @return \DateTime 
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }

 /**
     * Set store
     *
     * @param Store $store
     * @return Comment
     */
    public function setStore(Store $store = null)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get store
     *
     * @return Store 
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Return addedDate formatted to usi in production in comments list
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function getProdTimestamp()
    {
        $date = $this->getAddedDate();
        return $date->format('F j, Y') . ' at ' . $date->format('g:i a');
    }
}
