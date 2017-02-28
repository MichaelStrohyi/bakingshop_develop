<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\MenuItem;

class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['attr' => ['autocomplete' => 'off']])
            ->add('url', null, ['attr' => ['autocomplete' => 'off']])
            ->add('position', 'hidden', ['data' => MenuItem::DEFAULT_POSITION, 'attr' => ['class' => 'item-position']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MenuItem::class,
        ));
    }

    public function getName()
    {
        return 'admin_menu_item';
    }
}
