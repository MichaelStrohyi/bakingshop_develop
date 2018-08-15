<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Store;

class StoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['attr' => ['autocomplete' => 'off']])
            ->add('link', null, ['attr' => ['autocomplete' => 'off']])
            ->add('metaKeywords', 'textarea', ['attr' => ['rows' => '3']])
            ->add('metaDescription', 'textarea', ['attr' => ['rows' => '3']])
            ->add('metatags', 'textarea', ['attr' => ['rows' => '3']])
            ->add('keywords', 'textarea', ['attr' => ['rows' => '3']])
            ->add('description', 'textarea', ['attr' => ['rows' => '5']])
            ->add('logo', new ImageType, ['required' => false, 'data_class' => '\AppBundle\Entity\StoreLogo', 'label' => false])
            ->add('feedId', null, ['attr' => ['autocomplete' => 'off'], 'label' => 'FMTC Id'])
            ->add('is_featured', null, ['label' => 'Featured store'])
            ->add('activity', null, ['label' => 'Active store'])
            ->add('useFeedLinks', null, ['label' => 'Use feed links for coupons'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Store',
        ));
    }


    public function getName()
    {
        return 'admin_store';
    }
}