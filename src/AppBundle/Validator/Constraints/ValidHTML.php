<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidHTML extends Constraint 
{
    public $message = 'This value is not a valid meta tags list';
}