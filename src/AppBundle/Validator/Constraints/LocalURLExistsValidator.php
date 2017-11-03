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

        # get current validating MenuItem object and PageRepository
        $menu_item = $this->context->getObject();
        $repo = $this->em->getRepository('USPCPageBundle:Page');

        # validate url of current MenuItem
        $validation_result = $repo->validateURL($menu_item->getUrl());

        # if url is not valid
        if (is_array($validation_result)) {
            $new_url = '';
            $message = '';

            # select error message according to validation error
            switch ($validation_result['error']) {
                case $repo::URL_IS_INVALID:
                    $message = $constraint->url_invalid;
                    break;
                case $repo::URL_IS_ALIAS:
                    $message = $constraint->url_is_alias;
                    $new_url = $validation_result['new_url'];
                    break;
                default:
                    break;
            }
            
            # create violation with error message
            $this->context->buildViolation($message)
                ->setParameter('%url%', $value)
                ->setParameter('%new_url%', $new_url)
                ->addViolation();
        }
    }
}