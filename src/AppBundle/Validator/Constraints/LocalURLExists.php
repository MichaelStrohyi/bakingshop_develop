<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LocalURLExists extends Constraint 
{
    public $message = 'Page with URL "%url%" does not exist';
}