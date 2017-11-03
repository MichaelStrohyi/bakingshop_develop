<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\StoreLogo;

class StoreLogoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', 'file', ['label' => 'New logo', 'attr' => ['accept' => '.gif, .jpg, .jpeg, .png']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => StoreLogo::class,
        ));
    }

    public function getName()
    {
        return 'admin_store_logo';
    }
}
