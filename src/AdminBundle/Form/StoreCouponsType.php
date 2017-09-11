<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Store;

class StoreCouponsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('coupons', 'collection', [
                    'type' => new StoreCouponType,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'sort_by' => [
                        'position' => 'asc',
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Store::class,
        ));
    }

    public function getName()
    {
        return 'admin_store_coupons';
    }
}
