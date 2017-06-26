<?php

namespace PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT p FROM PostBundle:Post p ORDER BY p.date DESC ";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),5
        );

        return $this->render('PostBundle:Default:index.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/{slug}",name="display_post")
     */
    public function slugAction($slug, Request $request){

        $em = $this -> getDoctrine() -> getManager();

        $res = substr($slug,0,1);
        $newslug = substr($slug,1);

        if( $res == '1'){



            $category = $em -> getRepository('CategoryBundle:Category')->findOneBySlug($newslug);

            if($category){
                $dql   = "SELECT p FROM PostBundle:Post p LEFT JOIN p.categories c WHERE c.slug = '".$newslug."' ORDER BY p.date DESC  ";

                $query = $em->createQuery($dql);

                $paginator  = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $query,
                    $request->query->getInt('page', 1),5
                );

                return $this->render('PostBundle:Default:index.html.twig', array('pagination' => $pagination));

            }
        }elseif($res == '0'){

            $post = $em -> getRepository('PostBundle:Post')->findOneBySlug($newslug);

            if($post){
                return $this->render('PostBundle:Post:show.html.twig', array('post' => $post));
            }

        }
        return $this->redirectToRoute("post_default_index");
    }
}
