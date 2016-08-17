<?php

namespace Acme\Bundle\FrontBundle\Controller;

use Acme\Bundle\AdminBundle\Entity\Company;
use Acme\Bundle\AdminBundle\Entity\StudioBrandFurniture;
use Acme\Bundle\AdminBundle\Entity\StudioImage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
use Acme\Bundle\AdminBundle\Entity\BrandFurniture;
use Acme\Bundle\AdminBundle\Entity\BrandFurnitureOpinions;

/**
 * Class BrandFurnitureController
 * @package Acme\Bundle\AdminBundle\Controller
 */
class BrandFurnitureController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List studios
     * @Route("/producenci-mebli", name="front_brand-furniture")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->view_data['breadcrumbs'][] = array('title'=>'producenci mebli','url'=>$this->get('router')->generate('front_brand-furniture'));
        $this->view_data['global']['subtitle'] = 'Producenci mebli kuchennych';


        $dql   = "SELECT a FROM AcmeAdminBundle:BrandFurniture a";
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            12/*limit per page*/
        );
        $this->view_data['pagination'] = $pagination;


        return $this->render('AcmeFrontBundle:BrandFurniture:index.html.twig', $this->view_data);
    }
    /**
     * show one brand
     * @Route("/producenci-mebli/{slug}", name="front_brand-furniture_brand")
     */
    public function brand($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('AcmeAdminBundle:BrandFurniture')->findOneBy(array('slug'=>$slug));
        if(!$brand) return $this->redirectToRoute('front_brand-furniture');

        $this->view_data['breadcrumbs'][] = array('title'=>'producenci mebli','url'=>$this->get('router')->generate('front_brand-furniture'));
        $this->view_data['breadcrumbs'][] = array('title'=>$brand->getName(),'url'=>$this->get('router')->generate('front_brand-furniture_brand',array('slug'=>$brand->getSlug())));
        $this->view_data['brand'] = $brand;
        $this->view_data['global']['subtitle'] = 'Producenct mebli kuchennych - '.$brand->getName();

        return $this->render('AcmeFrontBundle:BrandFurniture:brand/brand.html.twig', $this->view_data);
    }
    /**
     * show and add comments for company device
     * @Route("/producenci-mebli/{slug}/opinie", name="front_brand-furniture_brand_opinions")
     */
    public function opinions(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('AcmeAdminBundle:BrandFurniture')->findOneBy(array('slug'=>$slug));
        if(!$brand) return $this->redirectToRoute('front_brand-furniture');



        $brandOpinions = new BrandFurnitureOpinions();

        $form = $this->createFormBuilder($brandOpinions)
        ->add('author', 'text',array('label'=>'Nazwa'))
        ->add('email', 'text',array('label'=>'Email'))
        ->add('description', 'textarea', array('label'=>'Treść','required'=>true,'attr' => array('rows' => '10')))
        ->add('captcha', 'captcha')
        ->add('save', 'submit', array('label' => 'Dodaj opinie'))
        ->getForm();

        $form->handleRequest($request);
        if($form->isValid())
        {
            $brandOpinions->setDateAdd(new \DateTime());
            $brandOpinions->setDateUpdate(new \DateTime());
            $brandOpinions->setIdBrandFurniture($brand);
            $brandOpinions->setIp($request->getClientIp());

            $em = $this->getDoctrine()->getManager();
            $em->persist($brandOpinions);
            $em->flush();
            $this->addFlash('success','Opinia została dodana. Dziękujemy');
            return $this->redirectToRoute('front_brand-furniture_brand_opinions',array('slug'=>$slug));
        }

        //pagination for
        $dql   = "SELECT a FROM AcmeAdminBundle:BrandFurnitureOpinions a WHERE a.idBrandFurniture=:idBrandFurniture ORDER BY a.dateAdd DESC";
        $query = $em->createQuery($dql)->setParameter('idBrandFurniture', $brand);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        $this->view_data['pagination'] = $pagination;


        $this->view_data['form'] = $form->createView();
        $this->view_data['breadcrumbs'][] = array('title'=>'producenci mebli','url'=>$this->get('router')->generate('front_brand-furniture'));
        $this->view_data['breadcrumbs'][] = array('title'=>$brand->getName(),'url'=>$this->get('router')->generate('front_brand-furniture_brand',array('slug'=>$brand->getSlug())));
        $this->view_data['breadcrumbs'][] = array('title'=>"Opinie",'url'=>$this->get('router')->generate('front_brand-furniture_brand_opinions',array('slug'=>$brand->getSlug())));
        $this->view_data['brand'] = $brand;
        $this->view_data['global']['subtitle'] = 'Producenct mebli kuchennych - '.$brand->getName();

        return $this->render('AcmeFrontBundle:BrandFurniture:brand/opinions.html.twig', $this->view_data);
    }
}
