<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Redirect;

class RedirectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prodUrl', null, ['attr' => ['autocomplete' => 'off'], 'label' => 'Shown URL'])
            ->add('url', null, ['attr' => ['autocomplete' => 'off'], 'label' => 'Existing URL'])
            ->add('position', 'hidden', ['default' => Redirect::DEFAULT_POSITION, 'attr' => ['class' => 'item-position']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Redirect::class,
        ));
    }

    public function getName()
    {
        return 'admin_redirect';
    }
}
