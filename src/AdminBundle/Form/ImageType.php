<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', 'file', ['label' => 'New logo', 'attr' => ['accept' => '.gif, .jpg, .jpeg, .png']])
        ;
    }

    public function getName()
    {
        return 'admin_image_upload';
    }
}
