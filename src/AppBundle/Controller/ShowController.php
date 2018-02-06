<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Show;
use AppBundle\Type\ShowType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        return $this->render('show/list.html.twig');
    }
    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
        $show = new Show();
        $form = $this->createForm(ShowType::class, $show);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $generatedFileName = time().'_'.$show->getCategory()->getName().'.'.$show->getMainPicture()->guessClientExtension();
            $path = $this->getParameter('kernel.project_dir').'/web'.$this->getParameter('upload_directory_file');

            $show->getMainPicture()->move($path, $generatedFileName);

            $show->setMainPicture($generatedFileName);

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

    public function categoriesAction()
    {
        return $this->render('_includes/categories.html.twig',
            [
            'categories' => ['Web Design', 'HTML', 'Freebies', 'Javascript', 'CSS', 'Tutorials']
            ]);
    }
}