<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\FeedAffiliate;


class FeedAffiliatesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', 'collection', [
                    'type' => new FeedAffiliateType,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'sort_by' => [
                        'network' => 'asc',
                    ],
                    'label' => false,
                ])
        ;
    }

    public function getName()
    {
        return 'admin_feed_affiliates';
    }


}
