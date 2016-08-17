<?php

namespace Acme\Bundle\FrontBundle\Controller;

use Acme\Bundle\AdminBundle\Entity\Company;
use Acme\Bundle\AdminBundle\Entity\StudioBrandDevice;
use Acme\Bundle\AdminBundle\Entity\StudioImage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
use Acme\Bundle\AdminBundle\Entity\BrandDevice;

/**
 * Class ExpositionsController
 * @package Acme\Bundle\FrontBundle\Controller
 */
class ExpositionsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List studios
     * @Route("/ekspozycje", name="front_expositions")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $this->view_data['breadcrumbs'][] = array('title'=>'ekspozycje','url'=>$this->get('router')->generate('front_expositions'));
        $this->view_data['global']['subtitle'] = 'Ekspozycje';

        $dql   = "SELECT a FROM AcmeAdminBundle:Expositions a";
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            8/*limit per page*/
        );
        $this->view_data['pagination'] = $pagination;
        return $this->render('AcmeFrontBundle:Expositions:index.html.twig', $this->view_data);
    }

    
}