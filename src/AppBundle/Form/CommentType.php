<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Comment;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'textarea', ['attr' => ['rows' => '5', 'class' => 'comment-label'], 'label' => 'Comment'])
            ->add('author', null, ['attr' => ['autocomplete' => 'off', 'class' => 'comment-author'], 'label' => 'Name'])
            ->add('email', null, ['attr' => ['autocomplete' => 'off', 'class' => 'comment-email']])
            ->add('parentId', 'hidden', ['attr' => ['class' => 'comment-parent']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Comment::class,
        ));
    }

    public function getName()
    {
        return 'app_store_comment';
    }
}
