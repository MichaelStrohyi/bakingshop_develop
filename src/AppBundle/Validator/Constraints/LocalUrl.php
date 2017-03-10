<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
* @Annotation
**/
class LocalUrl extends Constraint
{
    public $message = 'The string "%string%" does not mutch a local URL.';
}