<?php
namespace AppBundle\Controller\API;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/categories", name="api_category_list")
     */
    public function listAction(SerializerInterface $serializer)
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();

       // $data = $serializer->serialize($categories, 'json');

        return $this->json($categories);
    }
}