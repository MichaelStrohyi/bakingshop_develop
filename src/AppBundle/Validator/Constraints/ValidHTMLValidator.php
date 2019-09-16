<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidHTMLValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!(empty(trim(strip_tags($value))) && strip_tags($value, '<meta>') == $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}