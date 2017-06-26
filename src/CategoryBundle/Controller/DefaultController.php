<?php

namespace CategoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CategoryBundle:Default:index.html.twig');
    }


    /**
     * @Route ("/test",name="test_category")
     */
    public function getAllCategories()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CategoryBundle:Category');
        $categories = $repository->findAll();
        dump($categories);
        return $this->render('CategoryBundle:layout.html.twig',array('categories'=>$categories));
    }
}
