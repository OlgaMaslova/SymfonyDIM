<?php
namespace AppBundle\Controller\API;


use AppBundle\Entity\Category;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\DeserializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

/**
 * @Route(name="api_category_")
 */
class CategoryController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/categories", name="list")
     *
     * @SWG\Get(
     *      path="/api/categories",
     *      summary="Returns the list of categories",
     *      @SWG\Response(
     *           response=200,
     *           description="Returns the list of categories",
     *           @SWG\Schema(
     *               type="array",
     *               @Model(type=Category::class)
     *           )
     *      )
     * )
     */
    public function listAction(SerializerInterface $serializer)
    {
        //throw new \Exception("ouch");

        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();

        return $this->returnResponse($serializer->serialize($categories, 'json'), Response::HTTP_OK);
    }

    /**
     * @Method({"GET"})
     * @Route("/categories/{id}", name="get", requirements={"id"="\d+"})
     *
     * @SWG\Get(
     *      path="/api/categories/{id}",
     *      summary="Returns the category by his id",
     *      @SWG\Response(
     *          response=200,
     *          description="Returns the category by his id",
     *          @SWG\Schema(
     *              type="array",
     *              @Model(type=Category::class)
     *          )
     *      )
     * )
     */
    public function getCategoryAction(Category $category, SerializerInterface $serializer)
    {
        return $this->returnResponse($serializer->serialize($category, 'json'), Response::HTTP_OK);
    }

    /**
     * @Method({"POST"})
     * @Route("/categories", name="create")
     *
     * @SWG\Post(
     *     path="/api/categories",
     *     summary="Creates a category",
     *      @SWG\Parameter(
     *       name="JSON create body",
     *       in="body",
     *       description="json request object",
     *       type="json",
     *       required=true,
     *       @SWG\Schema(
     *        type="object",
     *        @SWG\Property(
     *                type="string",
     *                property="name",
     *                example="Drama"
     *           ),
     *       ),
     *      ),
     *      @SWG\Response(
     *       response=200,
     *       description="Category is updated"
     *      ),
     *      @SWG\Response(
     *       response=404,
     *       description="Validation error"
     *      )
     * )
     */
    public function createAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $serializationContext = DeserializationContext::create();

        $category = $serializer->deserialize($request->getContent(), Category::class, 'json', $serializationContext->setGroups(["category_create"]));

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
     *
     *  @SWG\Put(
     *     path="/api/categories/{id}",
     *     summary="Updates a category by its id",
     *      @SWG\Parameter(
     *       name="JSON update body",
     *       in="body",
     *       description="json request object",
     *       type="json",
     *       required=true,
     *       @SWG\Schema(
     *        type="object",
     *        @SWG\Property(
     *                type="string",
     *                property="name",
     *                example="Drama"
     *           ),
     *       ),
     *      ),
     *      @SWG\Response(
     *       response=200,
     *       description="Category is updated"
     *      ),
     *      @SWG\Response(
     *       response=404,
     *       description="Validation error"
     *      )
     * )
     */
    public function updateAction(Category $category, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $serializationContext = DeserializationContext::create();

        $newCategory = $serializer->deserialize($request->getContent(), Category::class, 'json', $serializationContext->setGroups(["category_create"]));

        $constraintValidationList = $validator->validate($newCategory);

        if($constraintValidationList->count() == 0) {
            $category->update($newCategory);

            $this->getDoctrine()->getManager()->flush();

            return $this->returnResponse('Category updated', Response::HTTP_OK);
        }

        return $this->returnResponse($serializer->serialize($constraintValidationList, 'json'), Response::HTTP_BAD_REQUEST);
    }
    /**
     * @Method({"DELETE"})
     * @Route("/categories/{id}", name="delete", requirements={"id"="\d+"})
     *
     * @SWG\Delete(
     *      path="/api/categories/{id}",
     *      summary="Deletes the category by his id",
     *      @SWG\Response(
     *        response=200,
     *        description="Category is deleted"
     *      ),
     *     @SWG\Response(
     *       response=404,
     *       description="Error"
     *     )
     * )
     */
    public function deleteAction(Category $category)
    {
        //deletes also all shows created by the user
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        return $this->returnResponse('Category is deleted', Response::HTTP_OK);
    }


}