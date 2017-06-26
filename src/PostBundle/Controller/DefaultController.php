<?php

namespace PostBundle\Controller;

use CommentBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $this->getAllCategories();
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

        $this->getAllCategories();
        $em = $this -> getDoctrine() -> getManager();

        $check = substr($slug,0,7);
        $res = substr($slug,0,1);
        $newslug = substr($slug,1);

    if ($check == 'login'){

        $post = null;
        return $this->render('PostBundle:Post:show.html.twig', array('post' => $post));
    }

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

            $comment = new Comment();
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $comment->setDate(new \DateTime());

            $form = $this->createFormBuilder($comment)
                ->add('content', TextType::class, array('label' => 'Content : ', 'data' => ''))
                ->add('save', SubmitType::class, array('label' => 'Comment'))
                ->getForm();

            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $comment = $form->getData();
                $em->persist($comment);
                $em->flush();
            }

            if($post){
                return $this->render('PostBundle:Post:show.html.twig', array('post' => $post, "form" => $form->createView()));
            }

        }
        return $this->redirectToRoute("post_default_index");
    }

    public function getAllCategories()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CategoryBundle:Category');
        $categories = $repository->findAll();
        $this->get('twig')->addGlobal('categories', $categories);
    }
}
