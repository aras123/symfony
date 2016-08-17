<?php

namespace Acme\Bundle\AdminBundle\Controller;

use Acme\Bundle\AdminBundle\Entity\Blog;
use Acme\Bundle\AdminBundle\Form\BlogType;
use Acme\Bundle\AdminBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends BaseController
{
    public function __construct()
    {
        $this->view_data['title']['page'] = 'Blog';
        $this->view_data['title']['header'] = 'Blog';

    }
    /**
     * @Route("/blog", name="admin_blog")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $this->view_data['blog'] = $em->getRepository('AcmeAdminBundle:Blog')->findAll();

        //title
        $this->view_data['title']['subheader'] = 'Lista';

        return $this->render('AcmeAdminBundle:Blog:index.html.twig',$this->view_data);
    }
    /**
     * @Route("/blog/create", name="admin_blog_create")
     */
    public function create(Request $request)
    {
        $blog = new Blog();

        $form = $this->createForm(new BlogType(),$blog);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $blog->setDateAdd(new \DateTime());
            $blog->setDateUpdate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);
            $em->flush();
            $this->addFlash('success','Dodano wpis');
            return $this->redirectToRoute('admin_blog_edit',array('id'=>$blog->getId()));
        }
        //title
        $this->view_data['title']['subheader'] = 'Dodaj wpis';
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Blog:create.html.twig', $this->view_data);
    }
    /**
     * @Route("/blog/{id}/edit", name="admin_blog_edit", requirements={"id":"\d+"})
     */
    public function edit(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository('AcmeAdminBundle:Blog')->find($id);
        if(!$blog) return $this->redirectToRoute('admin_blog');

        $form = $this->createForm(new BlogType(),$blog);

        $form->handleRequest($request);
        if($form->isValid())
        {
            $blog->setDateUpdate(new \DateTime());
            $em->flush();
            $this->addFlash('success','Dane zostały zapisane');
            return $this->redirectToRoute('admin_blog_edit',array('id'=>$id));
        }
        $this->view_data['title']['subheader'] = 'Edytuj wpis';
        $this->view_data['post'] = $blog;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Blog:edit.html.twig',$this->view_data);
    }
    /**
     * @Route("/blog/{id}/delete", name="admin_blog_delete", requirements={"id":"\d+"})
     */
    public function delete(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository('AcmeAdminBundle:Blog')->find($id);
        if(!$blog) return $this->redirectToRoute('admin_blog');

        $form = $this->createFormBuilder($blog)
        ->add('delete', 'submit', array('label' => 'Usuń'))
        ->getForm();

        $form->handleRequest($request);
        if($form->isValid())
        {
            $em->remove($blog);
            $em->flush();
            $this->addFlash('success','Producent został usunięty');
            return $this->redirectToRoute('admin_blog');
        }
        $this->view_data['title']['subheader'] = 'Usuń ';
        $this->view_data['post'] = $blog;
        $this->view_data['form'] = $form->createView();
        return $this->render('AcmeAdminBundle:Blog:delete.html.twig',$this->view_data);
    }
    /**
     * @Route("/blog/{id}/delete/logo", name="admin_blog_delete_logo", requirements={"id":"\d+"})
     */
    public function delete_logo($id)
    {
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository('AcmeAdminBundle:Blog')->find($id);
        if(!$blog) return $this->redirectToRoute('admin_blog');

        $blog->file_image = '';
        $blog->setDateUpdate(new \DateTime());
        $em->persist($blog);
        $em->flush();
        $this->addFlash('success','Logo zostało usunięte');
        return $this->redirectToRoute('admin_blog_edit',array('id'=>$id));
    }

}
