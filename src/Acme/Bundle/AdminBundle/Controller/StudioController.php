<?php

namespace Acme\Bundle\AdminBundle\Controller;

use Acme\Bundle\AdminBundle\Entity\Company;
use Acme\Bundle\AdminBundle\Entity\StudioBrandDevice;
use Acme\Bundle\AdminBundle\Entity\Albums;
use Acme\Bundle\AdminBundle\Entity\StudioImage;
use Acme\Bundle\AdminBundle\Entity\AlbumImages;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
use Acme\Bundle\AdminBundle\Entity\Studio;
use Acme\Bundle\AdminBundle\Form\StudioType;
use Acme\Bundle\AdminBundle\Entity\StudioBrandFurniture;
use Acme\Bundle\AdminBundle\Entity\Expositions;

/**
 * Class StudioController
 * @package Acme\Bundle\AdminBundle\Controller
 */
class StudioController extends BaseController
{
    public function __construct()
    {
        $this->view_data['title']['page'] = 'Studia kuchenne';
        $this->view_data['title']['header'] = 'Studia kuchenne';

    }

    /**
     * List studios
     * @Route("/studio", name="admin_studio")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $this->view_data['studios'] = $em->getRepository('AcmeAdminBundle:Studio')->findAll();

        //title
        $this->view_data['title']['subheader'] = 'Lista';

        return $this->render('AcmeAdminBundle:Studio:index.html.twig', $this->view_data);
    }

    /**
     * Create studio
     * @param Request $request
     * @param integer $idc - id company
     * @Route("/studio/create/{idc}", name="admin_studio_create")
     */
    public function create(Request $request,$idc=0)
    {
        $em = $this->getDoctrine()->getManager();
        $studio = new Studio();
        $form = $this->createForm(new StudioType(), $studio);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $studio->setDateAdd(new \DateTime());
            $studio->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($studio);
            $em->flush();
            $this->addFlash('success', 'Dodano studio');
            return $this->redirectToRoute('admin_studio_edit', array('id' => $studio->getId()));
        }
        //title
        $this->view_data['title']['subheader'] = 'Dodaj';
        if(isset($idc) AND $idc > 0 ) {
            $company = $em->getRepository('AcmeAdminBundle:Company')->find($idc);
            if($company) {
                $this->view_data['company'] = $company;
            }
        }

        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:create.html.twig', $this->view_data);
    }

    /**
     * Update studio
     * @param Request $request
     * @param integer $id Id studio
     *
     * @Route("/studio/{id}/edit", name="admin_studio_edit", requirements={"id":"\d+"})
     */
    public function edit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($id);
        if (!$studio) $this->redirectToRoute('admin_studio');


        $form = $this->createForm(new StudioType(), $studio);
        $form->add('active', 'checkbox', array('label' => 'Aktywne', 'required' => false));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $studio->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($studio);
            $em->flush();
            $this->addFlash('success', 'Zapisano zmiany');
            return $this->redirectToRoute('admin_studio_edit', array('id' => $studio->getId()));
        }
        //title
        $this->view_data['title']['subheader'] = 'Edycja';
        $this->view_data['studio'] = $studio;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:edit.html.twig', $this->view_data);
    }

    /**
     * Delete studio
     * @param Request $request
     * @param integer $id Id Studio
     *
     * @Route("/studio/{id}/delete", name="admin_studio_delete", requirements={"id":"\d+"})
     */
    public function delete(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($id);
        if (!$studio) return $this->redirectToRoute('admin_studio');

        $form = $this->createFormBuilder($studio)
        ->add('delete', 'submit', array('label' => 'Usuń'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->remove($studio);
            $em->flush();
            $this->addFlash('success', 'Studio zostąło usunięte');
            return $this->redirectToRoute('admin_studio');
        }
        $this->view_data['title']['subheader'] = 'Usuń ';
        $this->view_data['company'] = $studio;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:delete.html.twig', $this->view_data);
    }

    /**
     * Add/Delete brand furniture for studio
     * @param Request $request
     * @param integer $id Id studio
     * @param integer $delete_id Id StudioBrandFurniture delete
     *
     * @Route("/studio/{id}/brand-furniture/{delete_id}",name="admin_studio_brand-furniture", requirements={"id":"\d+"})
     */
    public function brandFurnitureAction(Request $request, $id, $delete_id = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($id);
        if (!$studio) return $this->redirectToRoute('admin_studio');
        //delete row if $delete>0
        if ($delete_id > 0) {
            $brandDelete = $em->getRepository('AcmeAdminBundle:StudioBrandFurniture')->findOneBy(['idStudio' => $id, 'id' => $delete_id]);
            $em->remove($brandDelete);
            $em->flush();
            $this->addFlash('success', 'Producent został usunięty z tego studia');
            return $this->redirectToRoute('admin_studio_brand-furniture', array('id' => $id));
        }
        //end delete
        $brand = new StudioBrandFurniture();
        $brand->setIdStudio($studio);
        $form = $this->createFormBuilder($brand)
        ->add('id_brand_furniture', 'entity', array('class' => 'Acme\Bundle\AdminBundle\Entity\BrandFurniture','placeholder'=>'Wybierz producenta', 'property' => 'name', 'label' => 'Producent','query_builder' => function(\Acme\Bundle\AdminBundle\Entity\BrandFurnitureRepository $er) use($id) {
            return $er->findOtherForStudio($id);
        },))
        ->add('save', 'submit', array('label' => 'Dodaj do studia'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {

            $em->persist($brand);
            $em->flush();
            $this->addFlash('success', 'Producent mebli został dodany do studia');
            return $this->redirectToRoute('admin_studio_brand-furniture', array('id' => $id));
        }
        //title
        $this->view_data['title']['subheader'] = 'Producenci przypisani do studia';
        $this->view_data['studio'] = $studio;
        $this->view_data['brandFurniture'] = $em->getRepository('AcmeAdminBundle:StudioBrandFurniture')->findBy(['idStudio' => $id]);

        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:brandFurniture.html.twig', $this->view_data);
    }
    /**
     * Add/Delete furniture device for studio
     * @param Request $request
     * @param integer $id Id studio
     * @param integer $delete_id Id StudiBrandDevice delete
     *
     * @Route("/studio/{id}/brand-device/{delete_id}",name="admin_studio_brand-device", requirements={"id":"\d+"})
     */
    public function brandDeviceAction(Request $request, $id, $delete_id = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($id);
        if (!$studio) return $this->redirectToRoute('admin_studio');
        //delete row if $delete>0
        if ($delete_id > 0) {
            $brandDelete = $em->getRepository('AcmeAdminBundle:StudioBrandDevice')->findOneBy(['idStudio' => $id, 'id' => $delete_id]);
            $em->remove($brandDelete);
            $em->flush();
            $this->addFlash('success', 'Producent został usunięty z tego studia');
            return $this->redirectToRoute('admin_studio_brand-device', array('id' => $id));
        }
        //end delete
        $brand = new StudioBrandDevice();

        $brand->setIdStudio($studio);
        $form = $this->createFormBuilder($brand)
        ->add('id_brand_device', 'entity', array('class' => 'Acme\Bundle\AdminBundle\Entity\BrandDevice', 'property' => 'name', 'placeholder'=>'Wybierz producenta', 'label' => 'Producent','query_builder' => function(\Acme\Bundle\AdminBundle\Entity\BrandDeviceRepository $er) use($id) {
            return $er->findOtherForStudio($id);
        },))
        ->add('save', 'submit', array('label' => 'Dodaj do studia'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {

            $em->persist($brand);
            $em->flush();
            $this->addFlash('success', 'Producent urządzeń został dodany do studia');
            return $this->redirectToRoute('admin_studio_brand-device', array('id' => $id));
        }
        //title
        $this->view_data['title']['subheader'] = 'Producenci przypisani do studia';
        $this->view_data['studio'] = $studio;
        $this->view_data['brandDevice'] = $em->getRepository('AcmeAdminBundle:StudioBrandDevice')->findBy(['idStudio' => $id]);

        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:brandDevice.html.twig', $this->view_data);
    }
    /**
     * Images for studio
     * @Route("/studio/{id}/images",name="admin_studio_images", requirements={"id":"\d+"})
     */
    public function images(Request $request, $id)
    {
        //check if is studio
        $em = $this->getDoctrine()->getManager();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($id);
        if (!$studio) return $this->redirectToRoute('admin_studio');

        $form = $this->createFormBuilder()
        ->add('file_image','file',array('label'=>'Zdjęcia','multiple'=>true))
        ->add('save', 'submit', array('label' => 'Dodaj'))
        ->getForm();

        $image_errors = array();
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            $file_count = count($form["file_image"]->getData());

            if($file_count>0) {
                foreach($form["file_image"]->getData() as $file_key=>$file) {

                    $studioImage = new StudioImage();
                    $studioImage->setIdStudio($studio);
                    $studioImage->file_image = $file;

                    $validator = $this->get('validator');
                    $errors = $validator->validate($studioImage);
                    if(count($errors)==0) {
                        $em->persist($studioImage);
                        $em->flush();
                    }else {
                        foreach($errors as $error) {
                            $image_errors[$file_key]['file_name'] = $file->getClientOriginalName();
                            $image_errors[$file_key]['errors'][] = $error->getMessage();
                        }
                    }
                }
                $this->addFlash('success','Zdjęcia zostały dodane');
            //create error message if exists
                if(count($image_errors)>0) {
                    $error_message = '<b>Wystąpiły błędy:</b>';
                    foreach($image_errors as $image_errors_k=>$image_errors_v) {
                        $error_message .= '<br/>Plik: <u>'.$image_errors_v['file_name'].'</u>';
                        $error_message .= '<ul><li>'.implode('</li><li>',$image_errors_v['errors']).'</li></ul>';
                    }
                    $this->addFlash('error',$error_message);
                }
                return $this->redirectToRoute('admin_studio_images', array('id' => $id));
            }
        }

        //get images for studio
        $studioImages = $em->getRepository('AcmeAdminBundle:StudioImage')->findBy(array('idStudio'=>$id));

        $this->view_data['title']['subheader'] = 'Zdjęcia';
        $this->view_data['form'] = $form->createView();
        $this->view_data['studioImages'] = $studioImages;

        $this->view_data['studio'] = $studio;

        return $this->render('@AcmeAdmin/Studio/images/images.twig',$this->view_data);
    }

    /**
     * Update image studio
     * @param Request $request
     * @param integer $id Id image studio
     *
     * @Route("/studio/images/edit/{id}", name="admin_studio_images_edit", requirements={"id":"\d+"})
     */
    public function images_edit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $studioImage = $em->getRepository('AcmeAdminBundle:StudioImage')->find($id);
        if (!$studioImage) $this->redirectToRoute('admin_studio');

        $idStudio = $studioImage->getIdStudio()->getId();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($idStudio);

        $form = $this->createFormBuilder($studioImage)
        ->add('title','text',array('label'=>'Nazwa'))
        ->add('save', 'submit', array('label' => 'Zapisz'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($studioImage);
            $em->flush();
            $this->addFlash('success', 'Zapisano zmiany');
            return $this->redirectToRoute('admin_studio_images_edit', array('id' => $studioImage->getId()));
        }
        //title
        $this->view_data['title']['subheader'] = 'Edycja zdjęcia: '.$studioImage->getTitle();
        $this->view_data['studioImage'] = $studioImage;
        $this->view_data['studio'] = $studio;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:images/images_edit.html.twig', $this->view_data);
    }
    /**
     * Update image studio
     * @param Request $request
     * @param integer $id Id image studio
     *
     * @Route("/studio/images/editajax", name="admin_studio_images_edit_ajax")
     */
    public function images_edit_ajax(Request $request)
    {
        echo 'test';
    }

    /**
     * Delete image
     * @param Request $request
     * @param integer $id id images studia
     * @Route("/studio/images/delete/{id}",name="admin_studio_images_delete", requirements={"id":"\d+"})
     */
    public function images_delete(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $studioImage = $em->getRepository('AcmeAdminBundle:StudioImage')->find($id);
        if (!$studioImage) return $this->redirectToRoute('admin_studio');

        $idStudio = $studioImage->getIdStudio()->getId();
        $form = $this->createFormBuilder($studioImage)
        ->add('delete', 'submit', array('label' => 'Usuń'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->remove($studioImage);
            $em->flush();
            $this->addFlash('success', 'Zdjęcie zostało usunięte');
            return $this->redirectToRoute('admin_studio_images', array('id' => $idStudio));
        }
        $this->view_data['title']['subheader'] = 'Usuń ';
        $this->view_data['studio'] = $studioImage;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:delete.html.twig', $this->view_data);
    }

    /**
     * show albums
     * @param integer $id id studio
     * @Route("/studio/{id}/albums",name="admin_studio_albums", requirements={"id":"\d+"})
     */
    public function albums(Request $request, $id)
    {
        //check if is studio
        $em = $this->getDoctrine()->getManager();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($id);
        if (!$studio) return $this->redirectToRoute('admin_studio');

        $albums = new Albums();
        $albums->setIdStudio($studio);
        $form = $this->createFormBuilder($albums)
        ->add('name','text',array('label'=>'Nazwa albumu'))
        ->add('save', 'submit', array('label' => 'Dodaj album'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $albums->setDateAdd(new \DateTime());
            $albums->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($albums);
            $em->flush();
            $this->addFlash('success', 'Album został dodany');
            return $this->redirectToRoute('admin_studio_albums', array('id' => $id));
        }

        //get images for studio
        $albums = $em->getRepository('AcmeAdminBundle:Albums')->findBy(array('idStudio'=>$id));

        $this->view_data['title']['subheader'] = 'Albumy';
        $this->view_data['form'] = $form->createView();
        $this->view_data['albums'] = $albums;

        $this->view_data['studio'] = $studio;

        return $this->render('@AcmeAdmin/Studio/albums/albums.html.twig',$this->view_data);
    } 
    /**
     * show albums
     * @param integer $id id album
     * @Route("/studio/albums/{id}/edit",name="admin_studio_albums_edit", requirements={"id":"\d+"})
     */
    public function albums_edit(Request $request, $id)
    {
        //check if is album
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('AcmeAdminBundle:Albums')->find($id);
        if (!$album) return $this->redirectToRoute('admin_studio');

        $form = $this->createFormBuilder($album)
        ->add('name','text',array('label'=>'Nazwa albumu'))
        ->add('save', 'submit', array('label' => 'Zapisz'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $album->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($album);
            $em->flush();
            $this->addFlash('success', 'Album został zmieniony');
            return $this->redirectToRoute('admin_studio_albums_edit', array('id' => $id));
        }

        $this->view_data['title']['subheader'] = 'Albumy';
        $this->view_data['form'] = $form->createView();

        $this->view_data['studio'] = $album->getIdStudio();

        return $this->render('@AcmeAdmin/Studio/albums/album_edit.html.twig',$this->view_data);
    }    

    /**
     * Delete image
     * @param Request $request
     * @param integer $id id album studia
     * @Route("/studio/albums/{id}/delete",name="admin_studio_albums_delete", requirements={"id":"\d+"})
     */
    public function albums_delete(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('AcmeAdminBundle:Albums')->find($id);
        if (!$album) return $this->redirectToRoute('admin_studio');

        $idStudio = $album->getIdStudio()->getId();
        $form = $this->createFormBuilder($album)
        ->add('delete', 'submit', array('label' => 'Usuń'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->remove($album);
            $em->flush();
            $this->addFlash('success', 'Zdjęcie zostało usunięte');
            return $this->redirectToRoute('admin_studio_albums', array('id' => $idStudio));
        }
        $this->view_data['title']['subheader'] = 'Usuń ';
        $this->view_data['studio'] = $album;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:delete.html.twig', $this->view_data);
    }
    /**
     * Images for albums
     * @Route("/studio/albums/{id}/images",name="admin_studio_albums_images", requirements={"id":"\d+"})
     */
    public function albums_images(Request $request, $id)
    {
        //check if is studio
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('AcmeAdminBundle:Albums')->find($id);
        if (!$album) return $this->redirectToRoute('admin_studio');

        $form = $this->createFormBuilder()
        ->add('file_image','file',array('label'=>'Zdjęcia','multiple'=>true))
        ->add('save', 'submit', array('label' => 'Dodaj zdjęcia do albumu'))
        ->getForm();

        $image_errors = array();
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            $file_count = count($form["file_image"]->getData());

            if($file_count>0) {
                foreach($form["file_image"]->getData() as $file_key=>$file) {

                    $albumImage = new AlbumImages();
                    $albumImage->setIdAlbum($album);
                    $albumImage->file_image = $file;

                    $validator = $this->get('validator');
                    $errors = $validator->validate($albumImage);
                    if(count($errors)==0) {
                        $em->persist($albumImage);
                        $em->flush();
                    }else {
                        foreach($errors as $error) {
                            $image_errors[$file_key]['file_name'] = $file->getClientOriginalName();
                            $image_errors[$file_key]['errors'][] = $error->getMessage();
                        }
                    }
                }
                $this->addFlash('success','Zdjęcia zostały dodane');
            //create error message if exists
                if(count($image_errors)>0) {
                    $error_message = '<b>Wystąpiły błędy:</b>';
                    foreach($image_errors as $image_errors_k=>$image_errors_v) {
                        $error_message .= '<br/>Plik: <u>'.$image_errors_v['file_name'].'</u>';
                        $error_message .= '<ul><li>'.implode('</li><li>',$image_errors_v['errors']).'</li></ul>';
                    }
                    $this->addFlash('error',$error_message);
                }
                return $this->redirectToRoute('admin_studio_albums_images', array('id' => $id));
            }
        }

        //get images for studio
        $albumImages = $em->getRepository('AcmeAdminBundle:AlbumImages')->findBy(array('idAlbum'=>$id));

        $this->view_data['title']['subheader'] = 'Zdjęcia do albumu: '.$album->getName();
        $this->view_data['form'] = $form->createView();
        $this->view_data['albumImages'] = $albumImages;
        $this->view_data['album'] = $album;

        $this->view_data['studio'] = $album->getIdStudio();

        return $this->render('@AcmeAdmin/Studio/albums/album_images.html.twig',$this->view_data);
    }

    /**
     * Update image studio from album
     * @param Request $request
     * @param integer $id Id image studio from album
     *
     * @Route("/studio/albums/images/{id}/edit",name="admin_studio_albums_images_edit", requirements={"id":"\d+"})
     */
    public function albums_images_edit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $albumImage = $em->getRepository('AcmeAdminBundle:AlbumImages')->find($id);
        if (!$albumImage) $this->redirectToRoute('admin_studio');

        $idStudio = $albumImage->getIdAlbum()->getIdStudio()->getId();
        $studio = $em->getRepository('AcmeAdminBundle:Studio')->find($idStudio);

        $form = $this->createFormBuilder($albumImage)
        ->add('title','text',array('label'=>'Nazwa'))
        ->add('save', 'submit', array('label' => 'Zapisz'))
        ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($albumImage);
            $em->flush();
            $this->addFlash('success', 'Zapisano zmiany');
            return $this->redirectToRoute('admin_studio_albums_images_edit', array('id' => $id));
        }
        //title
        $this->view_data['title']['subheader'] = 'Edycja zdjęcia: '.$albumImage->getTitle();
        $this->view_data['albumImage'] = $albumImage;
        $this->view_data['studio'] = $studio;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Studio:albums/album_images_edit.html.twig', $this->view_data);
    }

}