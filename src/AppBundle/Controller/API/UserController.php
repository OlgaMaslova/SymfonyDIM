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
     */
    public function getAction(User $user, SerializerInterface $serializer)
    {
        $serializationContext = SerializationContext::create();

        return $this->returnResponse($serializer->serialize($user, 'json', $serializationContext->setGroups(["user"])), Response::HTTP_OK);
    }

    /**
     * @Method({"POST"})
     * @Route("/users", name="create")
     */
    public function createAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EncoderFactoryInterface $encoderFactory)
    {
        $serializationContext = DeserializationContext::create();

        $user = $serializer->deserialize($request->getContent(), User::class, 'json', $serializationContext->setGroups(["user_create", "user"]));

        //dump($user);die;

        $constraintValidationList = $validator->validate($user);

        if($constraintValidationList->count() == 0) {

            $encoder = $encoderFactory->getEncoder($user);
            $hashedPassword = $encoder->encodePassword($user->getPassword(), null);

            $user->setRoles(explode(', ', $user->getRoles()));
            $user->setPassword($hashedPassword);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->returnResponse('User created', Response::HTTP_CREATED);
        }

        return $this->returnResponse($serializer->serialize($constraintValidationList, 'json'), Response::HTTP_BAD_REQUEST);
    }

}