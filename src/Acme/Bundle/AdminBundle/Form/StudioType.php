<?php

namespace Acme\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                //->add('file_image','file',array('label'=>'Logo','required'=>false))
                ->add('email','text',array('label'=>'Email'))
                ->add('idCompany','entity',array('label'=>'Firma','class'=>'Acme\Bundle\AdminBundle\Entity\Company','property'=>'name'))
                ->add('city','text',array('label'=>'Miasto'))
                ->add('county','choice',array('label'=>'Województwo','choices'=>array(
                                                                        'dolnośląskie'=>'Dolnośląskie',
                                                                        'kujawsko-pomorskie'=>'Kujawsko-pomorskie',
                                                                        'lubelskie'=>'Lubelskie',
                                                                        'lubuskie'=>'Lubuskie',
                                                                        'łódzkie'=>'Łódzkie',
                                                                        'małopolskie'=>'Małopolskie',
                                                                        'mazowieckie'=>'Mazowieckie',
                                                                        'opolskie'=>'Opolskie',
                                                                        'podkarpackie'=>'Podkarpackie',
                                                                        'podlaskie'=>'Podlaskie',
                                                                        'pomorskie'=>'Pomorskie',
                                                                        'śląskie'=>'Śląskie',
                                                                        'świętokrzyskie'=>'Świętokrzyskie',
                                                                        'warmińsko-mazurskie'=>'Warmińsko-mazurskie',
                                                                        'wielkopolskie'=>'Wielkopolskie',
                                                                        'zachodniopomorskie'=>'Zachodniopomorskie'),'placeholder' => 'Wybierz województwo'))
                ->add('street','text',array('label'=>'Ulica'))
                ->add('home','text',array('label'=>'Nr. domu'))
                ->add('zipcode','text',array('label'=>'Kod pocztowy'))
                ->add('phone','text',array('label'=>'Telefon'))
                ->add('website','text',array('label'=>'Website'))
                ->add('description', 'ckeditor', array(
                                                    'label'=>'Opis studia',
                                                    'config' => array(
                                                        'uiColor' => '#ffffff',
                                                        'filebrowserBrowseRoute'           => 'elfinder',
                                                        'filebrowserBrowseRouteAbsolute'   => true,
                                                        )
                                                    ))
                ->add('save', 'submit', array('label' => 'Zapisz'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'acme_admin_bundle_company_type';
    }
}
