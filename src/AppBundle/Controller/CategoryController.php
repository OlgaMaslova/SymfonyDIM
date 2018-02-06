<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Type\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/category", name = "category_")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/create", name = "create")
     */
    public function createAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager(); //get Entity Manager (pattern), clear even if uses flush
            $em->persist($category); //Persist  - new record, only flush - object exists already
            $em->flush();

            $this->addFlash('success', 'You successfully added a new category!');

            return $this->redirectToRoute('show_list');
        }

        return $this->render('category/create.html.twig', ['categoryForm' => $form->createView()]);

    }
}