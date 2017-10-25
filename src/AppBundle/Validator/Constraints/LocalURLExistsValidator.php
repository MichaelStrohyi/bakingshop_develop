<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class LocalURLExistsValidator extends ConstraintValidator
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    private $em;

    /**
     * Default constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        # convert resource into string
        if (is_resource($value) && get_resource_type($value) == 'stream') {
            $value = stream_get_contents($value, -1, 0);
        }

        $menu_item = $this->context->getObject();
        $repo = $this->em->getRepository('USPCPageBundle:Page');



        if (!$repo->isUrlValid($menu_item->getUrl())) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%url%', $value)
                ->addViolation();
        }
    }
}