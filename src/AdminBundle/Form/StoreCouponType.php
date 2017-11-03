<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\StoreCoupon;

class StoreCouponType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'textarea')
            ->add('code', null, ['attr' => ['autocomplete' => 'off']])
            ->add('link', null, ['attr' => ['autocomplete' => 'off']])
            ->add('startDate', null, ['attr' => ['autocomplete' => 'off']])
            ->add('expireDate', null, ['attr' => ['autocomplete' => 'off']])
            ->add('expireDate', null, ['attr' => ['autocomplete' => 'off']])
            ->add('logo', new ImageType, ['required' => false, 'data_class' => 'AppBundle\Entity\CouponImage'])
            ->add('activity', 'hidden', ['default' => StoreCoupon::DEFAULT_ACTIVITY, 'attr' => ['class' => 'coupon-activity']])
            ->add('position', 'hidden', ['default' => StoreCoupon::DEFAULT_POSITION, 'attr' => ['class' => 'coupon-position']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => StoreCoupon::class,
        ));
    }

    public function getName()
    {
        return 'admin_store_coupon';
    }
}
