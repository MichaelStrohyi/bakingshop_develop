<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\FeedAffiliate;

class FeedAffiliateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('network', null, ['attr' => ['autocomplete' => 'off']])
            ->add('feed_affiliate_id', null, ['attr' => ['autocomplete' => 'off'], 'label' => 'FMTC affiliate id'])
            ->add('affiliate_id', null, ['attr' => ['autocomplete' => 'off'], 'label' => 'Bakingshop affiliate id'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FeedAffiliate::class,
        ));
    }

    public function getName()
    {
        return 'admin_feed_affiliate';
    }
}
