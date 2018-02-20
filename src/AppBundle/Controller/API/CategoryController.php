<?php
namespace AppBundle\Controller\API;


use AppBundle\Entity\Category;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /**
     * @Method({"POST"})
     * @Route("/categories", name="create")
     */
    public function createAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $category = $serializer->deserialize($request->getContent(), Category::class, 'json');

        $constraintValidationList = $validator->validate($category);

        if($constraintValidationList->count() == 0) {
           $em = $this->getDoctrine()->getManager();
           $em->persist($category);
           $em->flush();

           return $this->returnResponse('Category created', Response::HTTP_CREATED);
        }

        return $this->returnResponse($serializer->serialize($constraintValidationList, 'json'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Method({"PUT"})
     * @Route("/categories/{id}", name="update", requirements={"id"="\d+"})
     */
    public function updateAction(Category $category, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $newCategory = $serializer->deserialize($request->getContent(), Category::class, 'json');

        $constraintValidationList = $validator->validate($newCategory);

        if($constraintValidationList->count() == 0) {
            $category->update($newCategory);

            $this->getDoctrine()->getManager()->flush();

            return $this->returnResponse('Category updated', Response::HTTP_OK);
        }

        return $this->returnResponse($serializer->serialize($constraintValidationList, 'json'), Response::HTTP_BAD_REQUEST);
    }


}