<?php

namespace Acme\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text',array('label'=>'Nazwa'))
                ->add('description', 'textarea',array('label'=>'Opis','required'=>false))
                ->add('file_image','file',array('label'=>'Logo','required'=>false))
                ->add('save', 'submit', array('label' => 'Zapisz'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'acme_admin_bundle_exposition_type';
    }
}
