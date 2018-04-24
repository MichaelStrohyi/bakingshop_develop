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
            ->add('label', 'textarea', ['attr' => ['rows' => '3', 'class' => 'coupon-label']])
            ->add('code', null, ['attr' => ['autocomplete' => 'off', 'class' => 'coupon-code']])
            ->add('link', null, ['attr' => ['autocomplete' => 'off']])
            ->add('startDate', null, ['attr' => ['autocomplete' => 'off'], 'label' => 'Starts'])
            ->add('expireDate', null, [
                'attr' => ['autocomplete' => 'off'],
                'label' => 'Expires',
                'widget' => 'choice',
                'years' => range(date('Y') - 2, date('Y') + 19),
                'months' => range(1, 12),
                'days' => range(1, 31)
                ])
            ->add('logo', new ImageType, ['required' => false, 'data_class' => 'AppBundle\Entity\CouponImage', 'label' => false])
            ->add('activity', 'hidden', ['default' => StoreCoupon::DEFAULT_ACTIVITY, 'attr' => ['class' => 'coupon-activity']])
            ->add('position', 'hidden', ['default' => StoreCoupon::DEFAULT_POSITION, 'attr' => ['class' => 'coupon-position']])
            ->add('inStore', 'checkbox', ['label' => 'in store coupon', 'required' => false])
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
