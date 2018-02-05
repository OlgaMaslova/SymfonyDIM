<?php

namespace AppBundle\Controller;

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
}