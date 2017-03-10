<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
* @Annotation
**/
class LocalUrlValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (is_resource($value) && get_resource_type($value) == "stream") {
            $value = stream_get_contents($value, -1, 0);
        }

        if (!preg_match('#^/[a-zA-Z0-9\_\.\-/]+$#', $value, $matches)){
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation()
            ;
        }
    }
}