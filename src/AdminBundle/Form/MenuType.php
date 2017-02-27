<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['attr' => ['autocomplete' => 'off']])
            ->add('header', null, ['attr' => ['autocomplete' => 'off']])
        ;
    }

    public function getName()
    {
        return 'admin_menu';
    }
}
