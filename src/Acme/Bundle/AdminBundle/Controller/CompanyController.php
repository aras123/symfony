<?php

namespace Acme\Bundle\AdminBundle\Controller;

use Acme\Bundle\AdminBundle\Controller\BaseController;
use Acme\Bundle\AdminBundle\Entity\BrandDevice;
use Acme\Bundle\AdminBundle\Entity\BrandFurniture;
use Acme\Bundle\AdminBundle\Entity\Company;
use Acme\Bundle\AdminBundle\Entity\StudioBrandFurniture;
use Acme\Bundle\AdminBundle\Form\CompanyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;

class CompanyController extends BaseController
{
    public function __construct()
    {
        $this->view_data['title']['page'] = 'Firmy';
        $this->view_data['title']['header'] = 'Firmy';

    }
    /**
     * List companies
     * @Route("/", name="admin_homepage")
     * @Route("/company", name="admin_company")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $this->view_data['companies'] = $em->getRepository('AcmeAdminBundle:Company')->findAll();
        //title
        $this->view_data['title']['subheader'] = 'Lista firm';

        return $this->render('AcmeAdminBundle:Company:index.html.twig',$this->view_data);
    }
    /**
     * Create new company
     * @param Request $request
     *
     * @Route("/company/create", name="admin_company_create")
     */
    public function create(Request $request)
    {

        $company = new Company();
        $form = $this->createForm(new CompanyType(),$company,array('validation_groups'=>array('create')));

        $form->handleRequest($request);
        if($form->isValid())
        {
            $company->setDateAdd(new \DateTime());
            $company->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();
            $this->addFlash('success','Dodano firmę');
            return $this->redirectToRoute('admin_company_edit',array('id'=>$company->getId()));
        }
        //title
        $this->view_data['title']['subheader'] = 'Dodaj firmę';
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Company:create.html.twig', $this->view_data);
    }

    /**
     * Update company information
     * @param Request $request
     * @param integer $id Id Company
     *
     * @Route("/company/{id}/edit", name="admin_company_edit", requirements={"id":"\d+"})
     */
    public function edit(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('AcmeAdminBundle:Company')->find($id);
        if(!$company) return $this->redirectToRoute('admin_company');

        $form = $this->createForm(new CompanyType(),$company);
        $form->add('password','password',array('label'=>'Hasło','required'=>false));
        $form->add('file_image','file',array('label'=>'Logo','required'=>false));
        $form->add('active','checkbox',array('label'=>'Aktywna','required'=>false));
        $old_password = $company->getPassword();
        $form->handleRequest($request);
        if($form->isValid())
        {
            if($form->get('password')->getData()!='') {
                $factory = $this->get('security.encoder_factory');
                $company_user = new Company();

                $encoder = $factory->getEncoder($company_user);
                $new_password = $encoder->encodePassword($form->get('password')->getData(), $company->getSalt());
                $company->setPassword($new_password);
            }else {
                $company->setPassword = $old_password;
            }

            $company->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();
            $this->addFlash('success','Zapisano zmiany');
           return $this->redirectToRoute('admin_company_edit',array('id'=>$company->getId()));
        }
        //title
        $this->view_data['title']['subheader'] = 'Edycja - '.$company->getName();
        $this->view_data['company'] = $company;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Company:edit.html.twig', $this->view_data);
    }
    /**
     * Delete company
     * @param Request $request
     * @param integer $id Id Company
     *
     * @Route("/company/{id}/delete", name="admin_company_delete", requirements={"id":"\d+"})
     */
    public function delete(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('AcmeAdminBundle:Company')->find($id);

        if(!$company) return $this->redirectToRoute('admin_company');

        $form = $this->createFormBuilder()->add('delete', 'submit', array('label' => 'Usuń'))->getForm();

        $form->handleRequest($request);
        if($form->isValid())
        {
            $em->remove($company);
            $em->flush();
            $this->addFlash('success','Firma '.$company->getName().' została usunięta');
            return $this->redirectToRoute('admin_company');
        }
        $this->view_data['title']['subheader'] = 'Usuń firmę - '.$company->getName();
        $this->view_data['company'] = $company;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Company:delete.html.twig',$this->view_data);
    }

    /**
     * List studios for company
     * @param integer $id Id Company
     *
     * @Route("/company/{id}/studios", name="admin_company_studios", requirements={"id":"\d+"})
     */
    public function studios($id)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('AcmeAdminBundle:Company')->find($id);
        if(!$company) return $this->redirectToRoute('admin_company');

        //title
        $this->view_data['title']['subheader'] = 'Studia przypisane do firmy '.$company->getName();
        $this->view_data['company'] = $company;
        return $this->render('AcmeAdminBundle:Company:studios.html.twig', $this->view_data);
    }

    /**
     * Delete/clear logo company
     * @param integer $id Id Company
     * @Route("/company/{id}/delete/logo", name="admin_company_delete_logo", requirements={"id":"\d+"})
     */
    public function delete_logo($id)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('AcmeAdminBundle:Company')->find($id);
        if(!$company) return $this->redirectToRoute('admin_company');

        $company->file_image = '';
        $company->setDateUpdate(new \DateTime());
        $em->persist($company);
        $em->flush();
        $this->addFlash('success','Logo zostało usunięte');
        return $this->redirectToRoute('admin_company_edit',array('id'=>$id));
    }


}
