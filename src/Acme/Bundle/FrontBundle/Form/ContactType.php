<?php

namespace Acme\Bundle\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name','text',array(
                                            'label'=>'Imię i Nazwisko / Nazwa Firmy',
                                            'constraints' => array(
                                                            new NotBlank(),
                                                            new Length(['max'=>100]),
                                                            )
                                            ))

                ->add('email','email',array(
                                            'label'=>'Email',
                                            'constraints' => array(
                                                            new NotBlank(),
                                                            new Length(['max'=>100]),
                                                            new Email()
                                                            )
                                            ))

                ->add('message','textarea',array(
                                            'label'=>'Treść',
                                            'attr' => array('rows' => '10'),
                                            'constraints' => array(
                                                            new NotBlank(),
                                                            new Length(['max'=>2000]),
                                                            )
                                            ))

                ->add('captcha', 'captcha')

                ->add('save', 'submit', array(
                                            'label' => 'Wyślij wiadomość'
                                            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'acme_front_bundle_contact_type';
    }
}
