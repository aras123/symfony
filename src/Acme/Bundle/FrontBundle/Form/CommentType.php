<?php

namespace Acme\Bundle\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('author', 'text',array('label'=>'Imię'))
                ->add('email', 'text',array('label'=>'Email'))
                ->add('description', 'textarea', array('label'=>'Treść','required'=>true,'attr' => array('rows' => '10')))
                ->add('captcha', 'captcha')
                ->add('save', 'submit', array('label' => 'Dodaj komentarz'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'acme_admin_bundle_comment_type';
    }
}
