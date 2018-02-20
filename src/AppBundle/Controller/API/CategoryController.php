<?php
namespace AppBundle\Controller\API;


use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;

/**
 * @Route(name="api_category_")
 */
class CategoryController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/categories", name="list")
     */
    public function listAction(SerializerInterface $serializer)
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();

        return $this->returnResponse($serializer->serialize($categories, 'json'), Response::HTTP_OK);
    }

    /**
     * @Method({"GET"})
     * @Route("/categories/{id}", name="get", requirements={"id"="\d+"})
     */
    public function getCategoryAction(Category $category, SerializerInterface $serializer)
    {
        return $this->returnResponse($serializer->serialize($category, 'json'), Response::HTTP_OK);
    }

}