<?php

namespace Veta\HomeworkBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Veta\HomeworkBundle\Entity\Post;
use Veta\HomeworkBundle\Form\PostType;

class PostController extends Controller
{
    /**
     * Show all Posts
     *
     * @Route("/post", name="index")
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Post", "veta_homework_post_index");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('VetaHomeworkBundle:Post')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($posts, $request->query->getInt('page', 1), 3);

        return $this->render('VetaHomeworkBundle:Post:index.html.twig', [
            'posts' => $posts,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @param Request $request
     */
    public function showAction(Request $request)
    {
        //Todo Show page Post
    }

    /**
     * Create data Post
     *
     * @Route("/post", name="create")
     * @Method("POST")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Post", "veta_homework_post_index");
        $breadcrumbs->addRouteItem("Create", "veta_homework_post_create");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $post = new Post();

        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateUrl('veta_homework_post_create'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();
                $message = 'The Post was created successfully. ';
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
            $this->addFlash('info', $message);

            return $this->redirectToRoute('veta_homework_post_index');
        }

        return $this->render('VetaHomeworkBundle:Site:form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Create Post',
            'li_active' => 'post',
            'route' => 'veta_homework_post_delete',

        ]);
    }

    /**
     * View data Post
     *
     * @Route("/post/{id}", name="view", requirements={"id": "\d+"})
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function viewAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Post", "veta_homework_post_index");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $id = $request->attributes->get('id');
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('VetaHomeworkBundle:Post')->find($id);

        $breadcrumbs->addRouteItem($post->getSlug(), "veta_homework_post_view", ['id' => $post->getId()]);

        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateUrl('veta_homework_post_edit', ['id' => $post->getId()]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        return $this->render('VetaHomeworkBundle:Site:form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Edit Post',
            'entity' => 'Post',
            'li_active' => 'post',
            'id' => $post->getId(),
            'route' => 'veta_homework_post_delete',

        ]);
    }

    /**
     * Edit data Post
     *
     * @Route("/post/{id}", name="edit", requirements={"id": "\d+"})
     * @Method("PUT")
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function editAction(Request $request)
    {
        $id = $request->attributes->get('id');
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('VetaHomeworkBundle:Post')->find($id);

        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateUrl('veta_homework_post_edit', ['id' => $id]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $em->persist($post);
            $em->flush();

            $this->addFlash('info', 'Edit Post with id '.$id);

            return $this->redirectToRoute('veta_homework_post_index');
        }
    }

    /**
     * Delete Post
     *
     * @Route("/post/{id}", name="delete", requirements={"id": "\d+"})
     * @Method("DELETE")
     *
     * @param Post $post
     * @return RedirectResponse
     */
    public function deleteAction(Post $post)
    {
        $id = $post->getId();
        $em = $this->getDoctrine()->getManager();

        $em->remove($post);
        $em->flush();

        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('VetaHomeworkBundle:Post')->findAll();

        return $this->render('VetaHomeworkBundle:Post:index.html.twig', [
            'posts' => $posts,
            'message' => 'Delete Post with id '.$id,
        ]);
    }
}
