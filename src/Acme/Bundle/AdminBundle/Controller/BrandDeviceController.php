<?php

namespace Acme\Bundle\AdminBundle\Controller;

use Acme\Bundle\AdminBundle\Entity\BrandDevice;
use Acme\Bundle\AdminBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Acme\Bundle\AdminBundle\Form\BrandType;

class BrandDeviceController extends BaseController
{
    public function __construct()
    {
        $this->view_data['title']['page'] = 'Producenci urządzeń';
        $this->view_data['title']['header'] = 'Producenci urządzeń';

    }
    /**
     * @Route("/brand-device", name="admin_brand-device")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $this->view_data['brands'] = $em->getRepository('AcmeAdminBundle:BrandDevice')->findAll();

        //title
        $this->view_data['title']['subheader'] = 'Lista';

        return $this->render('AcmeAdminBundle:BrandDevice:index.html.twig',$this->view_data);
    }
    /**
     * @Route("/brand-device/create", name="admin_brand-device_create")
     */
    public function create(Request $request)
    {
        $brand_device = new BrandDevice();

        $form = $this->createForm(new BrandType(),$brand_device);

        $form->handleRequest($request);
        if($form->isValid())
        {
            $brand_device->setDateAdd(new \DateTime());
            $brand_device->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($brand_device);
            $em->flush();
            $this->addFlash('success','Dodano producenta');
            return $this->redirectToRoute('admin_brand-device_edit',array('id'=>$brand_device->getId()));
        }
        //title
        $this->view_data['title']['subheader'] = 'Dodaj';
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:BrandDevice:create.html.twig', $this->view_data);
    }
    /**
     * @Route("/brand-device/{id}/edit", name="admin_brand-device_edit", requirements={"id":"\d+"})
     */
    public function edit(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $brand_device = $em->getRepository('AcmeAdminBundle:BrandDevice')->find($id);
        if(!$brand_device) return $this->redirectToRoute('admin_brand-device');

        $form = $this->createForm(new BrandType(),$brand_device);

        $form->handleRequest($request);
        if($form->isValid())
        {
            $brand_device->setDateUpdate(new \DateTime());
            $em->flush();
            $this->addFlash('success','Dane zostały zapisane');
            return $this->redirectToRoute('admin_brand-device_edit',array('id'=>$id));
        }
        $this->view_data['title']['subheader'] = 'Edytuj';
        $this->view_data['brand'] = $brand_device;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:BrandDevice:edit.html.twig',$this->view_data);
    }
    /**
     * @Route("/brand-device/{id}/delete", name="admin_brand-device_delete", requirements={"id":"\d+"})
     */
    public function delete(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $brand_device = $em->getRepository('AcmeAdminBundle:BrandDevice')->find($id);
        if(!$brand_device) return $this->redirectToRoute('admin_brand-device');

        $form = $this->createFormBuilder($brand_device)
            ->add('delete', 'submit', array('label' => 'Usuń'))
            ->getForm();

        $form->handleRequest($request);
        if($form->isValid())
        {
            $em->remove($brand_device);
            $em->flush();
            $this->addFlash('success','Producent został usunięty');
            return $this->redirectToRoute('admin_brand-device');
        }
        $this->view_data['title']['subheader'] = 'Usuń ';
        $this->view_data['brand'] = $brand_device;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:BrandDevice:delete.html.twig',$this->view_data);
    }
    /**
     * @Route("/brand-device/{id}/delete/logo", name="admin_brand-device_delete_logo", requirements={"id":"\d+"})
     */
    public function delete_logo($id)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('AcmeAdminBundle:BrandDevice')->find($id);
        if(!$brand) return $this->redirectToRoute('admin_brand-device');

        $brand->file_image = '';
        $brand->setDateUpdate(new \DateTime());
        $em->persist($brand);
        $em->flush();
        $this->addFlash('success','Logo zostało usunięte');
        return $this->redirectToRoute('admin_brand-device_edit',array('id'=>$id));
    }

}
