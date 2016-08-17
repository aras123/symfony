<?php

namespace Acme\Bundle\FrontBundle\Controller;

use Acme\Bundle\AdminBundle\Entity\Company;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
use Acme\Bundle\AdminBundle\Entity\CompanyOpinions;
use Acme\Bundle\AdminBundle\Entity\Conversations;
use Acme\Bundle\AdminBundle\Entity\ConversationsMessages;
use Acme\Bundle\FrontBundle\Form\ContactType;

/**
 * Class CompanyController
 * @package Acme\Bundle\AdminBundle\Controller
 */
class CompanyController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List companies
     * @Route("/firmy", name="front_company")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $this->view_data['breadcrumbs'][] = array('title'=>'firmy','url'=>$this->get('router')->generate('front_company'));
        $this->view_data['global']['subtitle'] = 'Firmy';

        #$dql   = "SELECT a FROM AcmeAdminBundle:Company a WHERE a.active=1";
        $query = $em->createQueryBuilder();
        $query->select('c')
              ->from('AcmeAdminBundle:Company', 'c')
              ->orderBy('c.id', 'DESC')
              ->where('c.active = 1')
              ->getQuery()
              ->getResult();
        $query = $em->createQuery($query);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        $this->view_data['pagination'] = $pagination;
        return $this->render('AcmeFrontBundle:Company:index.html.twig', $this->view_data);
    }
    /**
     * show one company
     * @Route("/firma/{slug}", name="front_company_company")
     */
    public function company($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$slug));
        if(!$company) return $this->redirectToRoute('front_company');

        $this->view_data['breadcrumbs'][] = array('title'=>'firmy','url'=>$this->get('router')->generate('front_company'));
        $this->view_data['breadcrumbs'][] = array('title'=>$company->getName(),'url'=>$this->get('router')->generate('front_company_company',array('slug'=>$company->getSlug())));
        $this->view_data['company'] = $company;
        $this->view_data['global']['subtitle'] = 'Firma - '.$company->getName();

        return $this->render('AcmeFrontBundle:Company:company/company.html.twig', $this->view_data);
    }

    /**
     * show studios for company
     * @Route("/firma/{slug}/studia", name="front_company_company_studios")
     */
    public function studios($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_company');

        $this->view_data['breadcrumbs'][] = array('title'=>'firmy','url'=>$this->get('router')->generate('front_company'));
        $this->view_data['breadcrumbs'][] = array('title'=>$company->getName(),'url'=>$this->get('router')->generate('front_company_company_studios',array('slug'=>$company->getSlug())));
        $this->view_data['company'] = $company;
        $this->view_data['studios'] = $em->getRepository('AcmeAdminBundle:Studio')->findBy(array('idCompany'=>$company,'active'=>1));
        $this->view_data['global']['subtitle'] = 'Studia dla firmy - '.$company->getName();

        return $this->render('AcmeFrontBundle:Company:company/studios.html.twig', $this->view_data);
    }

    /**
     * contact for company
     * @Route("/firma/{slug}/kontakt", name="front_company_company_contact")
     */
    public function contact(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$slug));
        if(!$company) return $this->redirectToRoute('front_company');



        $conversations = new Conversations();
        $conversationsMessages = new ConversationsMessages();

        $form = $this->createForm(new ContactType());

        $form->handleRequest($request);



        if($form->isValid())
        {
            $date_now = new \DateTime();

            $conversations->setIdCompany($company);
            $conversations->setHashCompany(md5('company'.time().rand(1,100)));
            $conversations->setHashClient(md5('client'.time().rand(101,200)));
            $conversations->setEmailClient($form->get('email')->getData());
            $conversations->setNameClient($form->get('name')->getData());
            $conversations->setDateAdd($date_now);

            $conversationsMessages->setMessage($form->get('message')->getData());
            $conversationsMessages->setType('client');
            $conversationsMessages->setIp($request->getClientIp());
            $conversationsMessages->setDateAdd($date_now);
            $conversationsMessages->setIdConverstion($conversations);

            $em = $this->getDoctrine()->getManager();
            $em->persist($conversations);
            $em->persist($conversationsMessages);
            $em->flush();
            $this->addFlash('success','Wiadomość została wysłana.<br/>Dodatkowo na maila otrzymałeś link do konwersacji z firmą.<br/>Gdy firma odpisze otrzymasz wiadomość na maila.');

            #send mail to company
            $link_company = $this->generateUrl('front_conversation',array('id'=>$conversations->getId(),'hash'=>$conversations->getHashCompany(),'type'=>'company'),true);
            $message = '<p>Twoja firma otrzymała wiadomość od <b>'.$form->get('name')->getData().'</b> przez <b>studiakuchenne.pl</b>.</p><p>Aby odczytać wiadomość i odpisać wejdź na: <a href="'.$link_company.'">'.$link_company.'</a></p>';
            $this->get('admin.mailer')->quick_mail(array(
                                                      'to' => $company->getEmail(),
                                                      'subject' => 'Nowa wiadomość od '.$form->get('name')->getData().' | StudiaKuchenne.pl',
                                                      'title'=>'Nowa wiadomość od '.$form->get('name')->getData(),
                                                      'message' => $message
                                                      ));
            #send mail to client
            $link_client = $this->generateUrl('front_conversation',array('id'=>$conversations->getId(),'hash'=>$conversations->getHashClient(),'type'=>'client'),true);
           $message = '<p>Dziękujemy za wysłanie wiadomości do firmy '.$company->getName().' przez <b>studiakuchenne.pl</b>.</p>
            			<p>Link do konwersacji: <a href="'.$link_client.'">'.$link_client.'</a></p>';

            $this->get('admin.mailer')->quick_mail(array(
                                                      'to' => $form->get('email')->getData(),
                                                      'subject' => 'Konwersacja z firmą '.$company->getName().' | StudiaKuchenne.pl',
                                                      'title'=>'Konwersacja z firmą '.$company->getName(),
                                                      'message' => $message
                                                      ));

            return $this->redirectToRoute('front_company_company_contact',array('slug'=>$slug));
        }





        $this->view_data['form'] = $form->createView();
        $this->view_data['breadcrumbs'][] = array('title'=>'firmy','url'=>$this->get('router')->generate('front_company'));
        $this->view_data['breadcrumbs'][] = array('title'=>$company->getName(),'url'=>$this->get('router')->generate('front_company_company',array('slug'=>$company->getSlug())));
        $this->view_data['breadcrumbs'][] = array('title'=>"Kontakt",'url'=>$this->get('router')->generate('front_company_company_contact',array('slug'=>$company->getSlug())));
        $this->view_data['company'] = $company;
        $this->view_data['global']['subtitle'] = 'Kontakt - '.$company->getName();

        return $this->render('AcmeFrontBundle:Company:company/contact.html.twig', $this->view_data);
    }
    /**
     * show and add comments for company
     * @Route("/firma/{slug}/opinie", name="front_company_company_opinions")
     */
    public function opinions(Request $request,$slug)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$slug));
        if(!$company) return $this->redirectToRoute('front_company');

        $companyOpinions = new CompanyOpinions();

        $form = $this->createFormBuilder($companyOpinions)
        ->add('author', 'text',array('label'=>'Nazwa'))
        ->add('email', 'text',array('label'=>'Email'))
        ->add('description', 'textarea', array('label'=>'Treść','required'=>true,'attr' => array('rows' => '10')))
        ->add('captcha', 'captcha')
        ->add('save', 'submit', array('label' => 'Dodaj opinie'))
        ->getForm();

        $form->handleRequest($request);
        if($form->isValid())
        {
            $companyOpinions->setDateAdd(new \DateTime());
            $companyOpinions->setDateUpdate(new \DateTime());
            $companyOpinions->setIdCompany($company);
            $companyOpinions->setIp($request->getClientIp());

            $em = $this->getDoctrine()->getManager();
            $em->persist($companyOpinions);
            $em->flush();
            $this->addFlash('success','Opinia została dodana. Dziękujemy');
            return $this->redirectToRoute('front_company_company_opinions',array('slug'=>$slug));
        }

        //pagination for
        $dql   = "SELECT a FROM AcmeAdminBundle:CompanyOpinions a WHERE a.idCompany=:idCompany ORDER BY a.dateAdd DESC";
        $query = $em->createQuery($dql)->setParameter('idCompany', $company);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        $this->view_data['pagination'] = $pagination;


        $this->view_data['form'] = $form->createView();

        $this->view_data['breadcrumbs'][] = array('title'=>'firmy','url'=>$this->get('router')->generate('front_company'));
        $this->view_data['breadcrumbs'][] = array('title'=>$company->getName(),'url'=>$this->get('router')->generate('front_company_company',array('slug'=>$company->getSlug())));
        $this->view_data['breadcrumbs'][] = array('title'=>"Opinie",'url'=>$this->get('router')->generate('front_company_company_opinions',array('slug'=>$company->getSlug())));



        $this->view_data['company'] = $company;

        return $this->render('AcmeFrontBundle:Company:company/opinions.html.twig', $this->view_data);
    }
}
