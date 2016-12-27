<?php

namespace Veta\HomeworkBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Veta\HomeworkBundle\Entity\Tag;
use Veta\HomeworkBundle\Form\TagType;

class TagController extends Controller
{
    /**
     * Show all Tag
     *
     * @Route("/tag", name="index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Tag", "veta_homework_tag_index");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('VetaHomeworkBundle:Tag')->findAll();

        return $this->render('VetaHomeworkBundle:Tag:index.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * Create data Tag
     *
     * @Route("/tag", name="create")
     * @Method("POST")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Tag", "veta_homework_tag_index");
        $breadcrumbs->addRouteItem("Create", "veta_homework_tag_create");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag, [
            'action' => $this->generateUrl('veta_homework_tag_create'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($tag);
                $em->flush();
                $message = 'The Tag was created successfully. ';
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
            $this->addFlash('info', $message);

            return $this->redirectToRoute('veta_homework_tag_index');
        }

        return $this->render('VetaHomeworkBundle:Site:form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Create Tag',
            'li_active' => 'tag',
            'route' => 'veta_homework_tag_delete',

        ]);
    }

    /**
     * View data Tag
     *
     * @Route("/tag/{slug}", name="view", requirements={"slug": "[\w\-]+"})
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function viewAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Tag", "veta_homework_tag_index");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $slug = $request->attributes->get('slug');
        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository('VetaHomeworkBundle:Tag')->findOneBy(['slug' => $slug]);

        $breadcrumbs->addRouteItem($slug, "veta_homework_tag_view", ['slug' => $slug]);

        $form = $this->createForm(TagType::class, $tag, [
            'action' => $this->generateUrl('veta_homework_tag_edit', ['id' => $tag->getId()]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        return $this->render('VetaHomeworkBundle:Site:form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Edit Tag',
            'entity' => 'Tag',
            'li_active' => 'tag',
            'id' => $tag->getId(),
            'route' => 'veta_homework_tag_delete',

        ]);
    }

    /**
     * Edit data Tag
     *
     * @Route("/tag/{id}", name="edit", requirements={"id": "\d+"})
     * @Method("PUT")
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function editAction(Request $request)
    {
        $id = $request->attributes->get('id');
        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository('VetaHomeworkBundle:Tag')->find($id);

        $form = $this->createForm(TagType::class, $tag, [
            'action' => $this->generateUrl('veta_homework_tag_edit', ['id' => $id]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();
            $em->persist($tag);
            $em->flush();

            $this->addFlash('info', 'Edit Tag with id '.$id);

            return $this->redirectToRoute('veta_homework_tag_index');
        }
    }

    /**
     * Delete Tag
     *
     * @Route("/tag/{id}", name="delete", requirements={"id": "\d+"})
     * @Method("DELETE")
     *
     * @param Tag $tag
     * @return RedirectResponse
     */
    public function deleteAction(Tag $tag)
    {
        $id = $tag->getId();
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tag);
            $em->flush();
            $message = 'Delete Tag with id '.$id;
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->addFlash('info', $message);
        return $this->redirectToRoute('veta_homework_tag_index');
    }
}
