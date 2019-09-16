<?php

namespace AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * Register this class as service using form.type_extension tag
 * 
 * services:
 *    app.form.extension:
 *      class: AppBundle\Form\Extension\AppFormTypeExtension
 *      tags:
 *        - { name: form.type_extension, alias: form }
 */
class AppFormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (null !== $options['default']) {
            $default = $options['default'];
            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($default) {
                    if (null === $event->getData()) {
                        $event->setData($default);
                    }
                }
            );
        }
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('default', null);
    }
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}