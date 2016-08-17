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
use Acme\Bundle\AdminBundle\Entity\Studio;
use Acme\Bundle\AdminBundle\Form\StudioType;
use Acme\Bundle\AdminBundle\Entity\StudioBrandFurniture;
use Acme\Bundle\AdminBundle\Entity\StudioOpinions;
use Acme\Bundle\AdminBundle\Entity\Albums;

/**
 * Class StudioController
 * @package Acme\Bundle\AdminBundle\Controller
 */
class StudioController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List studios
     * @Route("/studia", name="front_studios")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['global']['subtitle'] = 'Studia kuchenne';

        $query = $em->createQueryBuilder();
        $query->select('s')
              ->from('AcmeAdminBundle:Studio', 's')
              ->orderBy('s.id', 'DESC')
              ->Join('s.idCompany', 'c')
              ->where('s.active = 1')
              ->andWhere('c.active = 1')
              ->getQuery()
              ->getResult();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        $this->view_data['pagination'] = $pagination;

        #metatags
        $this->view_data['metatags']['title'] = 'Studia mebli kuchennych w Polsce';
        $this->view_data['metatags']['description'] = 'Zobacz wszystkie studia mebli kuchennych w Polsce w jednym miejscu';
        $this->view_data['metatags']['keywords'] = 'meble kuchenne, meble kuchenne polska, studia kuchenne, salony meblowe, salony kuchenne, meble do kuchni';

        return $this->render('AcmeFrontBundle:Studio:index.html.twig', $this->view_data);
    }
    /**
     * show studio
     * @Route("/studio/{company_slug}/{slug}", name="front_studios_studio")
     */
    public function studio($company_slug,$slug)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$company_slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_studios');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->findOneBy(array('slug'=>$slug,'idCompany'=>$company,'active'=>1));
        if (!$studio) return $this->redirectToRoute('front_studios');


        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['breadcrumbs'][] = array('title'=>'studio '.$studio->getIdCompany()->getName().' - '.$studio->getCity(),'url'=>$this->get('router')->generate('front_studios_studio',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['studio'] = $studio;
        $this->view_data['global']['subtitle'] = 'Studio kuchenne firmy <u>'.$studio->getIdCompany()->getName().'</u>';

        #metatagi
        $this->view_data['metatags']['title'] = 'Studio mebli kuchennych '.$studio->getIdCompany()->getName().' - '.$studio->getCity();
        $this->view_data['metatags']['description'] = 'Studio kuchenne '.$studio->getIdCompany()->getName().' w '.$studio->getCity();
        $this->view_data['metatags']['keywords'] = 'studio kuchenne '.$studio->getIdCompany()->getName().', kuchnie '.$studio->getIdCompany()->getName().', meble kuchenne '.$studio->getIdCompany()->getName().', '.$studio->getIdCompany()->getName().' '.$studio->getCity().', meble kuchenne '.$studio->getCity();

        return $this->render('AcmeFrontBundle:Studio:studio/studio.html.twig', $this->view_data);
    }
    /**
       DISABLE VERSION 5.11.2015 (remove @ from Route)
     * show brand device for studio
     * Route("/studio/{company_slug}/{slug}/producenci-urzadzen", name="front_studios_studio_brand-device")
     */
    public function studioBrandDevice($company_slug,$slug)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$company_slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_studios');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->findOneBy(array('slug'=>$slug,'idCompany'=>$company,'active'=>1));
        if (!$studio) return $this->redirectToRoute('front_studios');


        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['breadcrumbs'][] = array('title'=>'studio '.$studio->getIdCompany()->getName().' - '.$studio->getCity(),'url'=>$this->get('router')->generate('front_studios_studio',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['breadcrumbs'][] = array('title'=>'producenci urządzeń ','url'=>$this->get('router')->generate('front_studios_studio_brand-device',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['studio'] = $studio;

        #metatags
        $this->view_data['metatags']['title'] = 'Studia mebli kuchennych w Polsce';
        $this->view_data['metatags']['description'] = 'Zobacz wszystkie studia mebli kuchennych w Polsce w jednym miejscu';
        $this->view_data['metatags']['keywords'] = 'meble kuchenne, meble kuchenne polska, studia kuchenne, salony meblowe, salony kuchenne, meble do kuchni';

        return $this->render('AcmeFrontBundle:Studio:studio/brandDevice.html.twig', $this->view_data);
    }

    /**
      DISABLE VERSION 5.11.2015 (remove @ from Route)
     * show brand furniture for studio
     * Route("/studio/{company_slug}/{slug}/producenci-mebli-kuchennych", name="front_studios_studio_brand-furniture")
     */
    public function studioBrandFurniture($company_slug,$slug)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$company_slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_studios');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->findOneBy(array('slug'=>$slug,'idCompany'=>$company,'active'=>1));
        if (!$studio) return $this->redirectToRoute('front_studios');


        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['breadcrumbs'][] = array('title'=>'studio '.$studio->getIdCompany()->getName().' - '.$studio->getCity(),'url'=>$this->get('router')->generate('front_studios_studio',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['breadcrumbs'][] = array('title'=>'producenci urządzeń ','url'=>$this->get('router')->generate('front_studios_studio_brand-furniture',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['studio'] = $studio;

        #metatags
        $this->view_data['metatags']['title'] = 'Studia mebli kuchennych w Polsce';
        $this->view_data['metatags']['description'] = 'Zobacz wszystkie studia mebli kuchennych w Polsce w jednym miejscu';
        $this->view_data['metatags']['keywords'] = 'meble kuchenne, meble kuchenne polska, studia kuchenne, salony meblowe, salony kuchenne, meble do kuchni';

        return $this->render('AcmeFrontBundle:Studio:studio/brandFurniture.html.twig', $this->view_data);
    }
    /**
     * show contact form and contact info for studio
     * @Route("/studio/{company_slug}/{slug}/kontakt", name="front_studios_studio_contact")
     */
    public function studioContact($company_slug,$slug)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$company_slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_studios');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->findOneBy(array('slug'=>$slug,'idCompany'=>$company,'active'=>1));
        if (!$studio) return $this->redirectToRoute('front_studios');


        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['breadcrumbs'][] = array('title'=>'studio '.$studio->getIdCompany()->getName().' - '.$studio->getCity(),'url'=>$this->get('router')->generate('front_studios_studio',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['breadcrumbs'][] = array('title'=>'kontakt','url'=>$this->get('router')->generate('front_studios_studio_contact',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['studio'] = $studio;

        #metatags
        $this->view_data['metatags']['title'] = 'Studia mebli kuchennych w Polsce';
        $this->view_data['metatags']['description'] = 'Zobacz wszystkie studia mebli kuchennych w Polsce w jednym miejscu';
        $this->view_data['metatags']['keywords'] = 'meble kuchenne, meble kuchenne polska, studia kuchenne, salony meblowe, salony kuchenne, meble do kuchni';

        return $this->render('AcmeFrontBundle:Studio:studio/contact.html.twig', $this->view_data);
    }
    /**
     * show expositions for studio kitchem
     * @Route("/studio/{company_slug}/{slug}/ekspozycje", name="front_studios_studio_expositions")
     */
    public function studioExpositions(Request $request,$company_slug,$slug)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$company_slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_studios');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->findOneBy(array('slug'=>$slug,'idCompany'=>$company,'active'=>1));
        if (!$studio) return $this->redirectToRoute('front_studios');


        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['breadcrumbs'][] = array('title'=>'studio '.$studio->getIdCompany()->getName().' - '.$studio->getCity(),'url'=>$this->get('router')->generate('front_studios_studio',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['breadcrumbs'][] = array('title'=>'ekspozycje','url'=>$this->get('router')->generate('front_studios_studio_expositions',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['studio'] = $studio;

        #metatags
        $this->view_data['metatags']['title'] = 'Studia mebli kuchennych w Polsce';
        $this->view_data['metatags']['description'] = 'Zobacz wszystkie studia mebli kuchennych w Polsce w jednym miejscu';
        $this->view_data['metatags']['keywords'] = 'meble kuchenne, meble kuchenne polska, studia kuchenne, salony meblowe, salony kuchenne, meble do kuchni';

        return $this->render('AcmeFrontBundle:Studio:studio/expositions.html.twig', $this->view_data);
    }
    /**
     * show expositions for studio kitchem
     * @Route("/studio/{company_slug}/{slug}/ekspozycje/{slug_exposition}", name="front_exposition")
     */
    public function studioExposition(Request $request,$company_slug,$slug,$slug_exposition)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$company_slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_studios');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->findOneBy(array('slug'=>$slug,'idCompany'=>$company,'active'=>1));
        if (!$studio) return $this->redirectToRoute('front_studios');

        $exposition = $em->getRepository('AcmeAdminBundle:Expositions')->findOneBy(array('slug'=>$slug_exposition,'idStudio'=>$studio));
        if(!$exposition) return $this->redirectToRoute('front_studios');


        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['breadcrumbs'][] = array('title'=>'studio '.$studio->getIdCompany()->getName().' - '.$studio->getCity(),'url'=>$this->get('router')->generate('front_studios_studio',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['breadcrumbs'][] = array('title'=>'ekspozycje','url'=>$this->get('router')->generate('front_studios_studio_expositions',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['studio'] = $studio;
        $this->view_data['exposition'] = $exposition;

        #metatags
        $this->view_data['metatags']['title'] = 'Studia mebli kuchennych w Polsce';
        $this->view_data['metatags']['description'] = 'Zobacz wszystkie studia mebli kuchennych w Polsce w jednym miejscu';
        $this->view_data['metatags']['keywords'] = 'meble kuchenne, meble kuchenne polska, studia kuchenne, salony meblowe, salony kuchenne, meble do kuchni';

        return $this->render('AcmeFrontBundle:Studio:studio/exposition.html.twig', $this->view_data);
    }
    /**
     * show and add comments for studio
     * @Route("/studio/{company_slug}/{slug}/opinie", name="front_studios_studio_opinions")
     */
    public function opinions(Request $request,$company_slug,$slug)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$company_slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_studios');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->findOneBy(array('slug'=>$slug,'idCompany'=>$company,'active'=>1));
        if (!$studio) return $this->redirectToRoute('front_studios');

        $studioOpinions = new StudioOpinions();

        $form = $this->createFormBuilder($studioOpinions)
        ->add('author', 'text',array('label'=>'Nazwa'))
        ->add('email', 'text',array('label'=>'Email'))
        ->add('description', 'textarea', array('label'=>'Treść','required'=>true,'attr' => array('rows' => '10')))
        ->add('captcha', 'captcha')
        ->add('save', 'submit', array('label' => 'Dodaj opinie'))
        ->getForm();

        $form->handleRequest($request);
        if($form->isValid())
        {
            $studioOpinions->setDateAdd(new \DateTime());
            $studioOpinions->setDateUpdate(new \DateTime());
            $studioOpinions->setIdStudio($studio);
            $studioOpinions->setIp($request->getClientIp());

            $em = $this->getDoctrine()->getManager();
            $em->persist($studioOpinions);
            $em->flush();
            $this->addFlash('success','Opinia została dodana. Dziękujemy');
            return $this->redirectToRoute('front_studios_studio_opinions',array('company_slug'=>$company_slug,'slug'=>$slug));
        }

        //pagination for
        $dql   = "SELECT a FROM AcmeAdminBundle:StudioOpinions a WHERE a.idStudio=:idStudio ORDER BY a.dateAdd DESC";
        $query = $em->createQuery($dql)->setParameter('idStudio', $studio);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        $this->view_data['pagination'] = $pagination;
        $this->view_data['form'] = $form->createView();
        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['breadcrumbs'][] = array('title'=>'studio '.$studio->getIdCompany()->getName().' - '.$studio->getCity(),'url'=>$this->get('router')->generate('front_studios_studio',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['breadcrumbs'][] = array('title'=>'ekspozycje','url'=>$this->get('router')->generate('front_studios_studio_expositions',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['studio'] = $studio;

        #metatags
        $this->view_data['metatags']['title'] = 'Studia mebli kuchennych w Polsce';
        $this->view_data['metatags']['description'] = 'Zobacz wszystkie studia mebli kuchennych w Polsce w jednym miejscu';
        $this->view_data['metatags']['keywords'] = 'meble kuchenne, meble kuchenne polska, studia kuchenne, salony meblowe, salony kuchenne, meble do kuchni';

        return $this->render('AcmeFrontBundle:Studio:studio/opinions.html.twig', $this->view_data);
    }
    /**
     * show albums for studio kitchem
     * @Route("/studio/{company_slug}/{slug}/albumy", name="front_studios_studio_albums")
     */
    public function studioAlbums(Request $request,$company_slug,$slug)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$company_slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_studios');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->findOneBy(array('slug'=>$slug,'idCompany'=>$company,'active'=>1));
        if (!$studio) return $this->redirectToRoute('front_studios');


        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['breadcrumbs'][] = array('title'=>'studio '.$studio->getIdCompany()->getName().' - '.$studio->getCity(),'url'=>$this->get('router')->generate('front_studios_studio',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['breadcrumbs'][] = array('title'=>'albumy','url'=>$this->get('router')->generate('front_studios_studio_albums',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['studio'] = $studio;


        #metatags
        $this->view_data['metatags']['title'] = 'Studia mebli kuchennych w Polsce';
        $this->view_data['metatags']['description'] = 'Zobacz wszystkie studia mebli kuchennych w Polsce w jednym miejscu';
        $this->view_data['metatags']['keywords'] = 'meble kuchenne, meble kuchenne polska, studia kuchenne, salony meblowe, salony kuchenne, meble do kuchni';

        return $this->render('AcmeFrontBundle:Studio:studio/albums.html.twig', $this->view_data);
    }
    /**
     * show albums for studio kitchem
     * @Route("/studio/{company_slug}/{slug}/album/{id}", name="front_studios_studio_album")
     */
    public function studioAlbum(Request $request,$company_slug,$slug,$id)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AcmeAdminBundle:Company')->findOneBy(array('slug'=>$company_slug,'active'=>1));
        if(!$company) return $this->redirectToRoute('front_studios');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->findOneBy(array('slug'=>$slug,'idCompany'=>$company,'active'=>1));
        if (!$studio) return $this->redirectToRoute('front_studios');

        $album = $em->getRepository('AcmeAdminBundle:Albums')->findOneBy(array('idStudio'=>$studio,'id'=>$id));
        if (!$album) return $this->redirectToRoute('front_studios');

        $this->view_data['breadcrumbs'][] = array('title'=>'studia kuchenne','url'=>$this->get('router')->generate('front_studios'));
        $this->view_data['breadcrumbs'][] = array('title'=>'studio '.$studio->getIdCompany()->getName().' - '.$studio->getCity(),'url'=>$this->get('router')->generate('front_studios_studio',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['breadcrumbs'][] = array('title'=>'albumy','url'=>$this->get('router')->generate('front_studios_studio_albums',array('company_slug'=>$company_slug,'slug'=>$slug)));
        $this->view_data['studio'] = $studio;
        $this->view_data['album'] = $album;


        #metatags
        $this->view_data['metatags']['title'] = 'Studia mebli kuchennych w Polsce';
        $this->view_data['metatags']['description'] = 'Zobacz wszystkie studia mebli kuchennych w Polsce w jednym miejscu';
        $this->view_data['metatags']['keywords'] = 'meble kuchenne, meble kuchenne polska, studia kuchenne, salony meblowe, salony kuchenne, meble do kuchni';

        return $this->render('AcmeFrontBundle:Studio:studio/album.html.twig', $this->view_data);
    }
}
