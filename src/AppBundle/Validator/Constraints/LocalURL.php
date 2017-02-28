<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LocalURL extends Constraint 
{
    public $message = 'The string "%string%" does not mutch a local URL.';
}