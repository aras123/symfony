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
 * Class ConversationController
 * @package Acme\Bundle\AdminBundle\Controller
 */
class ConversationController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * show one company
     * @Route("/konwersacja/{id}/{hash}/{type}", name="front_conversation")
     */
    public function conversation(Request $request,$id,$hash,$type)
    {

        $where = array();
        $where['id'] = $id;

        if($type==='client') $where['hash_client'] = $hash;
        elseif($type=='company') $where['hash_company'] = $hash;
        else $where['hash_client'] = '';

        $em = $this->getDoctrine()->getManager();
        $conversation = $em->getRepository('AcmeAdminBundle:Conversations')->findOneBy($where);

        if(!$conversation) return $this->redirectToRoute('front_home');

        $form = $this->createForm(new ContactType());
        $form->remove('email');
        $form->remove('name');
        $form->remove('captcha');

        $form->handleRequest($request);



        if($form->isValid())
        {
            $conversationsMessages = new ConversationsMessages();
            $conversationsMessages->setMessage($form->get('message')->getData());
            $conversationsMessages->setType($type);
            $conversationsMessages->setIp($request->getClientIp());
            $conversationsMessages->setDateAdd(new \DateTime());
            $conversationsMessages->setIdConverstion($conversation);

            $em = $this->getDoctrine()->getManager();
            $em->persist($conversationsMessages);
            $em->flush();
            $this->addFlash('success','Wiadomość została wysłana.');

            #send mail
            $message = $this->generateUrl('front_conversation',array('id'=>$id,'hash'=>(($type=='client')?$conversation->getHashCompany():$conversation->getHashClient()),'type'=>(($type=='client')?'company':'client')),true).'#message-'.$conversationsMessages->getId();
            $this->get('admin.mailer')->quick_mail(array(
                                                      'to' => 'biuro@studiakuchenne.pl',
                                                      'subject' => 'Nowa wiadomość od '.(($type=='client')?$conversation->getNameClient():$conversation->getIdCompany()->getName()).' | StudiaKuchenne.pl',
                                                      'title'=>'Otrzymałeś nową wiadomość od '.(($type=='client')?$conversation->getNameClient():$conversation->getIdCompany()->getName()),
                                                      'message' => $message
                                                      ));

            return $this->redirectToRoute('front_conversation',array('id'=>$id,'hash'=>$hash,'type'=>$type));
        }

        //$this->view_data['breadcrumbs'][] = array('title'=>'firmy','url'=>$this->get('router')->generate('front_company'));
        //$this->view_data['breadcrumbs'][] = array('title'=>11111,'url'=>$this->get('router')->generate('front_company_company',array('slug'=>$company->getSlug())));
        $this->view_data['form'] = $form->createView();
        $this->view_data['company'] = $conversation->getIdCompany();
        $this->view_data['conversation'] = $conversation;
        $this->view_data['global']['subtitle'] = 'Test';

        return $this->render('AcmeFrontBundle:Conversation:conversation.html.twig', $this->view_data);
    }
}
