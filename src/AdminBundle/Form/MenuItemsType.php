<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Menu;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MenuItemsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', 'collection', [
                    'type' => new MenuItemType,
                    'allow_add' => true,
                    'allow_delete' => true,
                ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $submittedData = $event->getData();

            if (!array_key_exists('items', $submittedData)) {
                return;
            }

            //Re-index the array to ensure the forms stay in the submitted order.
            $submittedData['items'] = array_values($submittedData['items']);
            $event->setData($submittedData);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Menu::class,
        ));
    }

    public function getName()
    {
        return 'admin_menu_items';
    }
}
