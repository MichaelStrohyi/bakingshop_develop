<?php

namespace AdminBundle\Form;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Menu;

class MenusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', 'collection', [
                    'type' => new MenuPositionType,
                    'allow_add' => false,
                    'allow_delete' => false,
                    'by_reference' => false,
                    'sort_by' => [
                        'position' => 'asc',
                    ],
                    'label' => false,
                ])
        ;
    }

    public function getName()
    {
        return 'admin_menus';
    }


}
