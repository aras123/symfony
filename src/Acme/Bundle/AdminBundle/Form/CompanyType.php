<?php

namespace Acme\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file_image','file',array('label'=>'Logo','required'=>true))
                ->add('email','text',array('label'=>'Email'))
                ->add('password','password',array('label'=>'Hasło','required'=>true))
                ->add('name','text',array('label'=>'Nazwa'))
                ->add('street','text',array('label'=>'Ulica'))
                ->add('home','text',array('label'=>'Nr. domu'))
                ->add('zipcode','text',array('label'=>'Kod pocztowy'))
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
                ->add('phone','text',array('label'=>'Telefon'))
                ->add('nip','text',array('label'=>'NIP','required'=>false))
                ->add('website','text',array('label'=>'Website'))
                /*->add('description', 'ckeditor', array(
                                                    'label'=>'Opis firmy',
                                                    'config' => array(
                                                        'uiColor' => '#ffffff',
                                                        'filebrowserBrowseRoute'           => 'elfinder',
                                                        'filebrowserBrowseRouteAbsolute'   => true,
                                                        )
                                                    ))*/
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
