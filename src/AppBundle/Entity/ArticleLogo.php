<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ArticleLogo
 *
 * @ORM\Entity
 * @Vich\Uploadable
 */
class ArticleLogo extends Image
{
    /**
     *
     * @Vich\UploadableField(mapping="article_logo", fileNameProperty="filename")
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
