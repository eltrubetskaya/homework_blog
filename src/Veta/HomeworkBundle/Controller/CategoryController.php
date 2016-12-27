<?php

namespace Veta\HomeworkBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Veta\HomeworkBundle\Entity\Category;
use Veta\HomeworkBundle\Form\CategoryType;

class CategoryController extends Controller
{
    /**
     * Show all Category
     *
     * @Route("/category", name="index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Category", "veta_homework_category_index");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('VetaHomeworkBundle:Category')->findAll();

        return $this->render('VetaHomeworkBundle:Category:index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * Create data Category
     *
     * @Route("/category", name="create")
     * @Method("POST")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Category", "veta_homework_category_index");
        $breadcrumbs->addRouteItem("Create", "veta_homework_category_create");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('veta_homework_category_create'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();
                $message = 'The Category was created successfully. ';
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
            $this->addFlash('info', $message);

            return $this->redirectToRoute('veta_homework_category_index');
        }

        return $this->render('VetaHomeworkBundle:Site:form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Create Category',
            'li_active' => 'category',
            'route' => 'veta_homework_category_delete',

        ]);
    }

    /**
     * View data Category
     *
     * @Route("/category/{slug}", name="view", requirements={"slug": "[\w\-]+"})
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function viewAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Category", "veta_homework_category_index");
        $breadcrumbs->prependRouteItem("Home", "veta_homework_homepage");

        $slug = $request->attributes->get('slug');
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('VetaHomeworkBundle:Category')->findOneBy(['slug' => $slug]);

        $breadcrumbs->addRouteItem($slug, "veta_homework_category_view", ['slug' => $slug]);

        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('veta_homework_category_edit', ['id' => $category->getId()]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        return $this->render('VetaHomeworkBundle:Site:form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Edit Category',
            'entity' => 'Category',
            'li_active' => 'category',
            'id' => $category->getId(),
            'route' => 'veta_homework_category_delete',

        ]);
    }

    /**
     * Edit data Category
     *
     * @Route("/category/{id}", name="edit", requirements={"id": "\d+"})
     * @Method("PUT")
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function editAction(Request $request)
    {
        $id = $request->attributes->get('id');
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('VetaHomeworkBundle:Category')->find($id);

        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('veta_homework_category_edit', ['id' => $id]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em->persist($category);
            $em->flush();

            $this->addFlash('info', 'Edit Category with id '.$id);

            return $this->redirectToRoute('veta_homework_category_index');
        }
    }

    /**
     * Delete Category
     *
     * @Route("/category/{id}", name="delete", requirements={"id": "\d+"})
     * @Method("DELETE")
     *
     * @param Category $category
     * @return RedirectResponse
     */
    public function deleteAction(Category $category)
    {
        $id = $category->getId();

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            $message = 'Delete Category with id '.$id;
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->addFlash('info', $message);
        return $this->redirectToRoute('veta_homework_category_index');
    }
}
