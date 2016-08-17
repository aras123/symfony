<?php

namespace Acme\Bundle\AdminBundle\Controller;

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
use Acme\Bundle\AdminBundle\Form\ExpositionType;
use Acme\Bundle\AdminBundle\Entity\StudioBrandFurniture;
use Acme\Bundle\AdminBundle\Entity\Expositions;

/**
 * Class StudioController
 * @package Acme\Bundle\AdminBundle\Controller
 */
class ExpositionController extends BaseController
{
    public function __construct()
    {
        $this->view_data['title']['page'] = 'Studia';
        $this->view_data['title']['header'] = 'Studia';

    }

    /**
     * Expositions lists
     * @param $id id studio
     * @Route("/studio/{id}/expositions",name="admin_studio_expositions", requirements={"id":"\d+"})
     */
    public function index(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($id);
        if(!$studio) return $this->redirectToRoute('admin_studio');


        //add expostion
        $expositions = new Expositions();
        $expositions->setIdStudio($studio);
        $expositions->setDateAdd(new \DateTime());
        $expositions->setDateUpdate(new \DateTime());

        $form = $this->createForm(new ExpositionType(),$expositions,array('action'=>$this->get('router')->generate('admin_studio_expositions_create',array('id'=>$id))));

        $this->view_data['studioExpositions'] = $em->getRepository('AcmeAdminBundle:Expositions')->findBy(array('idStudio'=>$id));
        $this->view_data['studio'] = $studio;
        $this->view_data['form'] = $form->createView();
        $this->view_data['title']['subheader'] = 'Ekspozycja';
        return $this->render('AcmeAdminBundle:Studio:expositions/index.html.twig', $this->view_data);
    }
    /**
     * Add expositon
     * @param $id id studio
     * @Route("/studio/{id}/expositions/create",name="admin_studio_expositions_create", requirements={"id":"\d+"})
     */
    public function create(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($id);
        if(!$studio) return $this->redirectToRoute('admin_studio');


        //add expostion
        $expositions = new Expositions();
        $expositions->setIdStudio($studio);
        $expositions->setDateAdd(new \DateTime());
        $expositions->setDateUpdate(new \DateTime());

        $form = $this->createForm(new ExpositionType(),$expositions);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($expositions);
            $em->flush();
            $this->addFlash('success', 'Ekspozycja została dodana');
            return $this->redirectToRoute('admin_studio_expositions', array('id' =>$id));
        }

        $this->view_data['studio'] = $studio;
        $this->view_data['form'] = $form->createView();
        $this->view_data['title']['subheader'] = 'Ekspozycja - Dodaj';
        return $this->render('AcmeAdminBundle:Studio:expositions/create.html.twig', $this->view_data);
    }

    /**
     * Delete exposition
     * @param Request $request
     * @param integer $id id exposition
     * @Route("/studio/expositions/{id}/delete",name="admin_studio_expositions_delete", requirements={"id":"\d+"})
     */
    public function delete(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $studioExposition = $em->getRepository('AcmeAdminBundle:Expositions')->find($id);
        if (!$studioExposition) return $this->redirectToRoute('admin_studio');

        $idStudio = $studioExposition->getIdStudio()->getId();
        $form = $this->createFormBuilder($studioExposition)
        ->add('delete', 'submit', array('label' => 'Usuń'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->remove($studioExposition);
            $em->flush();
            $this->addFlash('success', 'Ekspozycja została usunięta');
            return $this->redirectToRoute('admin_studio_expositions', array('id' => $idStudio));
        }
        $this->view_data['title']['subheader'] = 'Usuń ';
        $this->view_data['studio'] = $studioExposition;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:delete.html.twig', $this->view_data);
    }
    /**
     * Update exposition
     * @param Request $request
     * @param integer $id Id exposition
     *
     * @Route("/studio/{id}/expositions/{id_exposition}/edit", name="admin_studio_expositions_edit", requirements={"id":"\d+","id_exposition":"\d+"})
     */
    public function edit(Request $request, $id,$id_exposition)
    {
        $em = $this->getDoctrine()->getManager();
        $studioExposition = $em->getRepository('AcmeAdminBundle:Expositions')->findOneBy(array('idStudio'=>$id,'id'=>$id_exposition));
        if (!$studioExposition) return $this->redirectToRoute('admin_studio');

        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($id);
        if(!$studio) return $this->redirectToRoute('admin_studio');

        $form = $this->createForm(new ExpositionType(),$studioExposition);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $studioExposition->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($studioExposition);
            $em->flush();
            $this->addFlash('success', 'Zapisano zmiany');
            return $this->redirectToRoute('admin_studio_expositions_edit', array('id' => $id, 'id_exposition'=>$id_exposition));
        }
        //title
        $this->view_data['title']['subheader'] = 'Edycja ekspozycji: '.$id;
        $this->view_data['studioExposition'] = $studioExposition;
        $this->view_data['studio'] = $studio;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:expositions/edit.html.twig', $this->view_data);
    }

    /**
     * Delete/clear logo company
     * @param integer $id Id exposition
     * @Route("/studio/{id}/expositions/{id_exposition}/delete/photo", name="admin_studio_expositions_delete_photo", requirements={"id":"\d+","id_exposition":"\d+"})
     */
    public function delete_photo(Request $request, $id, $id_exposition)
    {
        $em = $this->getDoctrine()->getManager();
        $exposition = $em->getRepository('AcmeAdminBundle:Expositions')->findOneBy(array('idStudio'=>$id,'id'=>$id_exposition));
        if(!$exposition) return $this->redirectToRoute('admin_studio');

        $exposition->file_image = '';
        $exposition->setDateUpdate(new \DateTime());
        $em->persist($exposition);
        $em->flush();
        $this->addFlash('success','Zdjęcie zostało usunięte');
        return $this->redirectToRoute('admin_studio_expositions_edit', array('id' => $id, 'id_exposition'=>$id_exposition));
    }
}