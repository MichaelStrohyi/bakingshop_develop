<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * StoreLogo
 *
 * @ORM\Entity
 */
class StoreLogo extends Image
{
    /**
     *
     * @Assert\Image(
     *     maxWidth = 200,
     *     maxHeight = 200,
     * )
     * @Assert\File(
     *     maxSize = "100k",
     * )
     *
     * @var File
     */
    protected $imageFile;
}
