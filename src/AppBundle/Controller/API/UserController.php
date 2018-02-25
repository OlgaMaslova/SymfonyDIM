<?php
namespace AppBundle\Controller\API;

use AppBundle\Entity\User;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;


/**
 * @Route(name="api_user_")
 */
class UserController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/users", name="list")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of users",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=User::class)
     *     )
     * )
     */
    public function listAction(SerializerInterface $serializer)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $serializationContext = SerializationContext::create();

        return $this->returnResponse($serializer->serialize($users, 'json', $serializationContext->setGroups(["user"])), Response::HTTP_OK);
    }

    /**
     * @Method({"GET"})
     * @Route("/users/{id}", name="get", requirements={"id"="\d+"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the user by his id",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=User::class)
     *     )
     * )
     */
    public function getAction(User $user, SerializerInterface $serializer)
    {
        $serializationContext = SerializationContext::create();

        return $this->returnResponse($serializer->serialize($user, 'json', $serializationContext->setGroups(["user"])), Response::HTTP_OK);
    }

    /**
     * @Method({"POST"})
     * @Route("/users", name="create")
     *
     *
     * @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="json order object",
     *       type="json",
     *       required=true,
     *     @SWG\Schema(
     *        type="object",
     *           @SWG\Property(
     *                type="string",
     *                property="fullname",
     *
     *                example="Toto"
     *              ),
     *           @SWG\Property(
     *                type="string",
     *                property="roles",
     *                example="ROLE_USER, ROLE_ADMIN"
     *              ),
     *          @SWG\Property(
     *                type="string",
     *                property="password",
     *                example="test"
     *              ),
     *          @SWG\Property(
     *                type="string",
     *                property="email",
     *                example="toto@mail.com"
     *              ),
     *     ),
     *  ),
     *  @SWG\Response(
     *       response=201,
     *       description="User is created"
     *    ),
     *  @SWG\Response(
     *     response=404,
     *     description="Validation error"
     *    )
     */
    public function createAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EncoderFactoryInterface $encoderFactory)
    {
        $serializationContext = DeserializationContext::create();

        $user = $serializer->deserialize($request->getContent(), User::class, 'json', $serializationContext->setGroups(["user_create", "user"]));


        $constraintValidationList = $validator->validate($user);

        if($constraintValidationList->count() == 0) {

            $encoder = $encoderFactory->getEncoder($user);
            $hashedPassword = $encoder->encodePassword($user->getPassword(), null);

            $user->setRoles(explode(', ', $user->getRoles()));
            $user->setPassword($hashedPassword);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->returnResponse('User is created', Response::HTTP_CREATED);
        }

        return $this->returnResponse($serializer->serialize($constraintValidationList, 'json'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Method({"PUT"})
     * @Route("/users/{id}", name="update", requirements={"id"="\d+"})
     */
    public function updateAction(User $user, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $serializationContext = DeserializationContext::create();

        $newUser = $serializer->deserialize($request->getContent(), User::class, 'json', $serializationContext->setGroups(["user", "user_update"]));

        $newUser->setPassword($user->getPassword());

        $constraintValidationList = $validator->validate($newUser);


        if($constraintValidationList->count() == 0) {
            $newUser->setRoles(explode(', ', $newUser->getRoles()));
            $user->update($newUser);

            $this->getDoctrine()->getManager()->flush();

            return $this->returnResponse('User is updated', Response::HTTP_OK);
        }

        return $this->returnResponse($serializer->serialize($constraintValidationList, 'json'), Response::HTTP_BAD_REQUEST);
    }
    /**
     * @Method({"DELETE"})
     * @Route("/users/{id}", name="delete", requirements={"id"="\d+"})
     */
    public function deleteAction(User $user)
    {
        //deletes also all shows created by the user
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->returnResponse('User is deleted', Response::HTTP_OK);
    }

}