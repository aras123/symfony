<?php

namespace Acme\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text',array('label'=>'Nazwa'))
                ->add('description', 'ckeditor', array(
                    'label'=>'Opis',
                    'required'=>true,
                    'config' => array(
                        'uiColor' => '#ffffff',
                        'filebrowserBrowseRoute'           => 'elfinder',
                        'filebrowserBrowseRouteAbsolute'   => true,
                        )
                    ))
                ->add('meta_title', 'text',array('label'=>'Metatagi - title','required'=>true))
                ->add('meta_keywords', 'text',array('label'=>'Metatagi - keywords','required'=>true))
                ->add('meta_description', 'text',array('label'=>'Metatagi - description','required'=>true))
                ->add('file_image','file',array('label'=>'Logo','required'=>false))
                ->add('save', 'submit', array('label' => 'Zapisz'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'acme_admin_bundle_blog_type';
    }
}
