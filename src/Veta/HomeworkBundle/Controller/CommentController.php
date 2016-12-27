<?php

namespace Veta\HomeworkBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Veta\HomeworkBundle\Entity\Comment;
use Veta\HomeworkBundle\Form\CommentType;

class CommentController extends Controller
{
    /**
     * Show all Comment
     *
     * @Route("/comment", name="index")
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Comment", "veta_homework_comment_index");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('VetaHomeworkBundle:Comment')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($comments, $request->query->getInt('page', 1), 3);

        return $this->render('VetaHomeworkBundle:Comment:index.html.twig', [
            'comments' => $comments,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Create data Comment
     *
     * @Route("/comment", name="create")
     * @Method("POST")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Comment", "veta_homework_comment_index");
        $breadcrumbs->addRouteItem("Create", "veta_homework_comment_create");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('veta_homework_comment_create'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('info', 'The Comment was created successfully.');

            return $this->redirectToRoute('veta_homework_comment_index');
        }

        return $this->render('VetaHomeworkBundle:Site:form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Create Comment',
            'li_active' => 'comment',
            'route' => 'veta_homework_comment_delete',

        ]);
    }

    /**
     * View data Comment
     *
     * @Route("/comment", name="view")
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function viewAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Comment", "veta_homework_comment_index");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $id = $request->attributes->get('id');
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('VetaHomeworkBundle:Comment')->find($id);

        $breadcrumbs->addRouteItem($comment->getPost()->getTitle(), "veta_homework_comment_view", ['id' => $id]);

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('veta_homework_comment_edit', ['id' => $id]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        return $this->render('VetaHomeworkBundle:Site:form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Edit Comment',
            'entity' => 'Comment',
            'li_active' => 'comment',
            'id' => $id,
            'route' => 'veta_homework_comment_delete',

        ]);
    }

    /**
     * Edit data Comment
     *
     * @Route("/comment/{id}", name="edit", requirements={"id": "\d+"})
     * @Method("PUT")
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function editAction(Request $request)
    {
        $id = $request->attributes->get('id');
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('VetaHomeworkBundle:Comment')->find($id);

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('veta_homework_comment_edit', ['id' => $id]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('info', 'Edit Comment with id '.$id);

            return $this->redirectToRoute('veta_homework_comment_index');
        }
    }

    /**
     * Delete Comment
     *
     * @Route("/comment/{id}", name="delete", requirements={"id": "\d+"})
     * @Method("DELETE")
     *
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function deleteAction(Comment $comment)
    {
        $id = $comment->getId();
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
            $message = 'Delete Comment with id '.$id;
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->addFlash('info', $message);
        return $this->redirectToRoute('veta_homework_comment_index');
    }
}
