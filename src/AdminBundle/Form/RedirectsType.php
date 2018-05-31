<?php

namespace AdminBundle\Form;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Redirect;

class RedirectsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', 'collection', [
                    'type' => new RedirectType,
                    'allow_add' => true,
                    'allow_delete' => true,
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
        return 'admin_redirects';
    }


}
