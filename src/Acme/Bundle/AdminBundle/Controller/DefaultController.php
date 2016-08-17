<?php

namespace Acme\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/city_search", name="city_search")
     */
    public function searchCityAction(Request $request)
    {
        $q = $request->get('term');
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository('AcmeAdminBundle:City')->findLikeName($q);
        return $this->render('@AcmeAdmin/Default/city.html.twig',array('results'=>$results));
    }
    /**
     * @Route("/city_get/{id}", name="city_get", defaults={"id":0})
     */
    public function getCityAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('AcmeAdminBundle:City')->find($id);

        return new Response($book->getName());
    }
}
