<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Menu;

class MenuPositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = ['' => null];
        foreach (Menu::getTypes() as $key => $value) {
            $choices[$value] = strtolower($value);
        }
        $builder
            ->add('position', 'hidden', ['attr' => ['class' => 'item-position']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Menu::class,
        ));
    }

    public function getName()
    {
        return 'admin_menu_position';
    }
}
