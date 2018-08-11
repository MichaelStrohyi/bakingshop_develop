<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Article;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = ['' => null];
        foreach (Article::getTypes() as $key => $value) {
            $choices[$value] = strtolower($value);
        }

        $builder
            ->add('header', null, ['attr' => ['autocomplete' => 'off']])
            ->add('url', null, ['attr' => ['autocomplete' => 'off']])
            ->add('author', null, ['attr' => ['autocomplete' => 'off']])
            ->add('type', 'choice', [
                'choices'  => $choices,
                'choice_attr' => function($key, $val, $index) {
                    $disabled = false;
                    if ($key === null) {
                        $disabled = true;
                    }

                    return $disabled ? ['disabled' => 'disabled'] : [];
                },
                'choices_as_values' => true,
            ])
            ->add('logo', new ImageType, ['required' => false, 'data_class' => '\AppBundle\Entity\ArticleLogo', 'label' => false])
            ->add('keywords', 'textarea', ['attr' => ['rows' => '3']])
            ->add('description', 'textarea', ['attr' => ['rows' => '3']])
            ->add('metatags', 'textarea', ['attr' => ['rows' => '3']])
            ->add('body', 'ckeditor')
            ->add('is_homepage', null, ['label' => 'Use as Homepage'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Article',
        ));
    }


    public function getName()
    {
        return 'admin_article';
    }
}