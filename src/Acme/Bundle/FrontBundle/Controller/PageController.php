<?php

namespace Acme\Bundle\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Acme\Bundle\FrontBundle\Form\ContactType;
use Acme\Bundle\AdminBundle\Entity\Studio;

class PageController extends BaseController
{
    /**
     * @Route("/", name="front_home")
     */
    public function indexAction()
    {


        $this->view_data['breadcrumbs'][] = array('title'=>'strona główna','url'=>$this->get('router')->generate('front_home'));
        $this->view_data['global']['subtitle'] = 'Strona główna';
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
              ->from('AcmeAdminBundle:Studio', 's')
              ->orderBy('s.id', 'DESC')
              ->Join('s.idCompany', 'c')
              ->where('s.active = 1')
              ->andWhere('c.active = 1')
              ->setMaxResults(6);
        $result = $qb->getQuery()->getResult();

        $this->view_data['studios'] = $result;
        $this->view_data['metatags']['title'] = 'Strona Główna';

    	return $this->render('AcmeFrontBundle:Page:index.html.twig',$this->view_data);
    }
    /**
     * @Route("/kontakt", name="front_contact")
     */
    public function contactAction(Request $request)
    {

    	$form = $this->createForm(new ContactType(),array());

    	$form->handleRequest($request);
    	if($form->isValid())
    	{
    		$formData = $form->getData();

        //send message
        $formData['message'] = 'Treść wiadomości:<br/><p>'.htmlspecialchars($formData['message']).'</p>';
        $this->get('admin.mailer')->quick_mail(array(
                                                  'from' => array($formData['email']=>$formData['name']),
                                                  'to' => 'biuro@studiakuchenne.pl',
                                                  'subject' => 'Kontakt | StudiaKuchenne.pl',
                                                  'title'=>'Wiadomość ze strony',
                                                  'message' => $formData['message']
                                                  ));

    		$this->addFlash('success','Wiadomość została wysłana');
            return $this->redirectToRoute('front_contact');
    	}

    	$this->view_data['form'] = $form->createView();
    	$this->view_data['global']['subtitle'] = 'Kontakt';
    	$this->view_data['metatags']['title'] = 'Kontakt';
    	$this->view_data['breadcrumbs'][] = array('title'=>'kontakt','url'=>$this->get('router')->generate('front_contact'));

    	return $this->render('AcmeFrontBundle:Page:contact.html.twig',$this->view_data);

    }
}
