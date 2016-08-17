<?php

namespace Acme\Bundle\FrontBundle\Controller;

use Acme\Bundle\AdminBundle\Entity\Company;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
use Acme\Bundle\AdminBundle\Entity\Blog;
use Acme\Bundle\AdminBundle\Entity\BlogComments;
use Acme\Bundle\FrontBundle\Form\CommentType;

/**
 * Class BlogController
 * @package Acme\Bundle\AdminBundle\Controller
 */
class BlogController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List studios
     * @Route("/blog", name="front_blog")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $this->view_data['breadcrumbs'][] = array('title'=>'blog','url'=>$this->get('router')->generate('front_blog'));
        $this->view_data['global']['subtitle'] = 'Blog';

        $dql   = "SELECT a FROM AcmeAdminBundle:Blog a ORDER BY a.dateAdd DESC";
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            8/*limit per page*/
            );
        $this->view_data['pagination'] = $pagination;

        //metatags
        $this->view_data['metatags']['title'] = 'Blog';
        $this->view_data['metatags']['keywords'] = 'blog studia, blog studia kuchenne';
        $this->view_data['metatags']['description'] = 'Blog na studiakuchenne.pl';

        return $this->render('AcmeFrontBundle:Blog:index.html.twig', $this->view_data);
    }
    /**
     * show one post
     * @Route("/blog/{slug}", name="front_blog_post")
     */
    public function post(Request $request,$slug)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AcmeAdminBundle:Blog')->findOneBy(array('slug'=>$slug));
        if(!$post) return $this->redirectToRoute('front_blog');


        $blogComments = new BlogComments();
        $form = $this->createForm(new CommentType(),$blogComments);

        $this->view_data['is_form_post'] = false;
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $blogComments->setDateAdd(new \DateTime());
                $blogComments->setDateUpdate(new \DateTime());
                $blogComments->setIdBlog($post);
                $blogComments->setIp($request->getClientIp());

                $em = $this->getDoctrine()->getManager();
                $em->persist($blogComments);
                $em->flush();
                $this->addFlash('success','Komentarz został dodany. Dziękujemy');
                return $this->redirectToRoute('front_blog_post',array('slug'=>$slug));
            }else {
                $this->view_data['is_form_post'] = true;
            }
        }
        //pagination for
        $dql   = "SELECT a FROM AcmeAdminBundle:BlogComments a WHERE a.idBlog=:idBlog ORDER BY a.dateAdd DESC";
        $query = $em->createQuery($dql)->setParameter('idBlog', $post);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
            );

        $this->view_data['pagination'] = $pagination;

        $this->view_data['form'] = $form->createView();
        $this->view_data['breadcrumbs'][] = array('title'=>'Blog','url'=>$this->get('router')->generate('front_blog'));
        $this->view_data['breadcrumbs'][] = array('title'=>$post->getName(),'url'=>$this->get('router')->generate('front_blog_post',array('slug'=>$post->getSlug())));
        $this->view_data['post'] = $post;
        $this->view_data['global']['subtitle'] = 'Blog - '.$post->getName();

        //metatags
        $this->view_data['metatags']['title'] = $post->getMetaTitle().' - Blog';
        $this->view_data['metatags']['keywords'] = $post->getMetaKeywords();
        $this->view_data['metatags']['description'] = $post->getMetaDescription();

        return $this->render('AcmeFrontBundle:Blog:post.html.twig', $this->view_data);
    }
}