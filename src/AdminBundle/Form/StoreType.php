<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Store;

class StoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['attr' => ['autocomplete' => 'off']])
            ->add('url', null, ['attr' => ['autocomplete' => 'off']])
            ->add('keywords', 'textarea')
            ->add('description', 'textarea')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Store',
        ));
    }


    public function getName()
    {
        return 'admin_store';
    }
}