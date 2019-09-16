<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LocalURLExists extends Constraint 
{
    public $url_invalid = 'Page with URL "%url%" does not exist';
    public $url_is_alias = 'Page with URL "%url%" has been moved to new URL "%new_url%"';
}