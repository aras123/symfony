<?php

namespace Acme\Bundle\AdminBundle\Controller;

use Acme\Bundle\AdminBundle\Controller\BaseController;
use Acme\Bundle\AdminBundle\Entity\BrandFurniture;
use Acme\Bundle\AdminBundle\Form\BrandType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandFurnitureController extends BaseController
{
    public function __construct()
    {
        $this->view_data['title']['page'] = 'Producenci mebli';
        $this->view_data['title']['header'] = 'Producenci mebli';

    }
    /**
     * @Route("/brand-furniture", name="admin_brand-furniture")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $this->view_data['brands'] = $em->getRepository('AcmeAdminBundle:BrandFurniture')->findAll();

        //title
        $this->view_data['title']['subheader'] = 'Lista';

        return $this->render('AcmeAdminBundle:BrandFurniture:index.html.twig',$this->view_data);
    }
    /**
     * @Route("/brand-furniture/create", name="admin_brand-furniture_create")
     */
    public function create(Request $request)
    {
        $brand = new BrandFurniture();

        $form = $this->createForm(new BrandType(),$brand);

        $form->handleRequest($request);
        if($form->isValid())
        {
            $brand->setDateAdd(new \DateTime());
            $brand->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($brand);
            $em->flush();
            $this->addFlash('success','Dodano producenta');
            return $this->redirectToRoute('admin_brand-furniture_edit',array('id'=>$brand->getId()));
        }
        //title
        $this->view_data['title']['subheader'] = 'Dodaj';
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:BrandFurniture:create.html.twig', $this->view_data);
    }
    /**
     * @Route("/brand-furniture/{id}/edit", name="admin_brand-furniture_edit", requirements={"id":"\d+"})
     */
    public function edit(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('AcmeAdminBundle:BrandFurniture')->find($id);
        if(!$brand) return $this->redirectToRoute('admin_brand-furniture');

        $form = $this->createForm(new BrandType(),$brand);


        $form->handleRequest($request);
        if($form->isValid())
        {
            $brand->setDateUpdate(new \DateTime());
            $em->persist($brand);
            $em->flush();
            $this->addFlash('success','Dane zostały zapisane');
            return $this->redirectToRoute('admin_brand-furniture_edit',array('id'=>$id));
        }
        $this->view_data['title']['subheader'] = 'Edytuj';
        $this->view_data['brand'] = $brand;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:BrandFurniture:edit.html.twig',$this->view_data);
    }
    /**
     * @Route("/brand-furniture/{id}/delete", name="admin_brand-furniture_delete", requirements={"id":"\d+"})
     */
    public function delete(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('AcmeAdminBundle:BrandFurniture')->find($id);
        if(!$brand) return $this->redirectToRoute('admin_brand-furniture');

        $form = $this->createFormBuilder($brand)
            ->add('delete', 'submit', array('label' => 'Usuń'))
            ->getForm();

        $form->handleRequest($request);
        if($form->isValid())
        {
            $em->remove($brand);
            $em->flush();
            $this->addFlash('success','Producent został usunięty');
            return $this->redirectToRoute('admin_brand-furniture');
        }
        $this->view_data['title']['subheader'] = 'Usuń ';
        $this->view_data['brand'] = $brand;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:BrandFurniture:delete.html.twig',$this->view_data);
    }
    /**
     * @Route("/brand-furniture/{id}/delete/logo", name="admin_brand-furniture_delete_logo", requirements={"id":"\d+"})
     */
    public function delete_logo($id)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('AcmeAdminBundle:BrandFurniture')->find($id);
        if(!$brand) return $this->redirectToRoute('admin_brand-furniture');

        $brand->file_image = '';
        $brand->setDateUpdate(new \DateTime());
        $em->persist($brand);
        $em->flush();
        $this->addFlash('success','Logo zostało usunięte');
        return $this->redirectToRoute('admin_brand-furniture_edit',array('id'=>$id));
    }

}
