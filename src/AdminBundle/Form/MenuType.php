<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Menu;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = ['' => null];
        foreach (Menu::getTypes() as $key => $value) {
            $choices[$value] = strtolower($value);
        }
        $builder
            ->add('name', null, ['attr' => ['autocomplete' => 'off']])
            ->add('header', null, ['attr' => ['autocomplete' => 'off']])
            ->add('type', 'choice', [
                'choices'  => $choices,
                'choice_attr' => function($key, $val, $index) {
                    $disabled = false;
                    if ($key === null) {
                        $disabled = true;
                    }

                    return $disabled ? ['disabled' => 'disabled'] : [];
                },
                'choices_as_values' => true,
            ])
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
        return 'admin_menu';
    }
}
