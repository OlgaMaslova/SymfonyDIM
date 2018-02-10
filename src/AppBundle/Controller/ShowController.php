<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Show;
use AppBundle\File\FileUploader;
use AppBundle\Type\ShowType;
use AppBundle\EventListener\ShowUploadListener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route(name="show_")
 */
class ShowController extends Controller
{
    /**
     * @Route("/", name="list")
     */
    public function listAction()
    {
        $shows = $this->getDoctrine()->getManager()->getRepository('AppBundle\Entity\Show')->findAll();
        return $this->render('show/list.html.twig', ['shows'=>$shows]);
    }
    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
        $show = new Show();
        $form = $this->createForm(ShowType::class, $show, ['validation_groups' => ['create']]);

        $form->handleRequest($request);

        if ($form->isValid()) {

/*
            $generatedFileName = $fileUploader->upload($show->getMainPicture(), $show->getCategory()->getName());

            $show->setMainPicture($generatedFileName);
*/
            $em = $this->getDoctrine()->getManager(); //get Entity Manager (pattern), clear even if uses flush
            $em->persist($show); //Persist  - new record, only flush - object exists already
            $em->flush();

            // upload file
            // Save
            $this->addFlash('success', 'You successfully added a new show!');

            return $this->redirectToRoute('show_list');
        }

        return $this->render('show/createShow.html.twig', ['showForm' => $form->createView()]);
    }

    /**
     * @Route("/update/{id}", name="update")
     */
    public function updateAction(Show $show, Request $request, FileUploader $fileUploader)
    {
        $showForm = $this->createForm(ShowType::class, $show, ['validation_groups' => ['update']]);
        $showForm->handleRequest($request);

        if ($showForm->isValid())
        {
/*
            $generatedFileName = $fileUploader->upload($show->getMainPicture(), $show->getCategory()->getName());

            $show->setMainPicture($generatedFileName);
*/
            $em = $this->getDoctrine()->getManager(); //get Entity Manager (pattern), clear even if uses flush
            $em->persist($show); //Persist  - new record, only flush - object exists already
            $em->flush();

            $this->addFlash('success', 'You successfully updated the show!');
            return $this->redirectToRoute('show_list');
        }

        return $this->render('show/createShow.html.twig', ['showForm' => $showForm->createView()]);
    }

    public function categoriesAction()
    {
        $categories = $this->getDoctrine()->getManager()->getRepository('AppBundle\Entity\Category')->findAll();
        return $this->render('_includes/categories.html.twig',
            [
            'categories' => $categories
            ]);
    }

}