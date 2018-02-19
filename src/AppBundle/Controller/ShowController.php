<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Show;
use AppBundle\File\FileUploader;
use AppBundle\ShowFinder\ShowFinder;
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
    public function listAction(Request $request, ShowFinder $showFinder)
    {
        $showRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Show');
        $session= $request->getSession();

        if($session->has('query_search_shows')) {
            $shows = $showFinder->searchByName($session->get('query_search_shows'));
/*
            //if the show exists in local database we present only this one
            if ($shows["Local Database"] != null) {
                $shows = $shows["Local Database"];
            } else {
                $shows =$shows["IMDB API"];
            }
*/
            $request->getSession()->remove('query_search_shows');
        } else {
            $shows = $showRepository->findAll();
        }

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
            $show->setDbSource(Show::DATA_SOURCE_DB);
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

    /**
     * @Route("/search", name="search")
     */

    public function searchAction(Request $request)
    {
        $request->getSession()->set('query_search_shows', $request->request->get('query'));

        return $this->redirectToRoute('show_list');
    }

    /**
     * @Route("/delete", name="delete")
     * @Method({"DELETE"})
     */
    /*
    public function deleteAction(Request $request, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $doctrine = $this->getDoctrine();
        $showId = $request->request->get('show_id');
        //$show = $this->getDoctrine()->getRepository('AppBundle:Show')->findOneBy(['id' => $showId]);
        if (!$show = $doctrine->getRepository('AppBundle:Show')->findOneById($showId)) {
            throw new NotFoundHttpException(sprintf('There is no show with the id %d', $showId));
        }
        $csrfToken = new CsrfToken('delete_show', $request->request->get('_csrf_token'));
        if ($csrfTokenManager->isTokenValid($csrfToken)) {
            $doctrine->getManager()->remove($show);
            $doctrine->getManager()->flush();
            $this->addFlash('success', 'The show have been successfully deleted.');
        } else {
            $this->addFlash('danger', 'Then csrf token is not valid. The deletion was not completed.');
        }
        return $this->redirectToRoute('show_list');
    }
*/
}