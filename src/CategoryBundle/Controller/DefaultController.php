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



    public function getAllCategories()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CategoryBundle:Category');
        $categories = $repository->findAll();
        return $this->render('CategoryBundle:Default:categories.html.twig',array('categories'=>$categories));
    }
}
