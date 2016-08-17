<?php

namespace Acme\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BrandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text',array('label'=>'Nazwa'))
                ->add('description', 'ckeditor', array(
                                                    'label'=>'Opis studia',
                                                    'config' => array(
                                                        'uiColor' => '#ffffff',
                                                        'filebrowserBrowseRoute'           => 'elfinder',
                                                        'filebrowserBrowseRouteAbsolute'   => true,
                                                        )
                                                    ))
                ->add('file_image','file',array('label'=>'Logo','required'=>false))
                ->add('website', 'text',array('label'=>'Website','required'=>false))
                ->add('save', 'submit', array('label' => 'Zapisz'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'acme_admin_bundle_brand_type';
    }
}
