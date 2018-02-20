<?php
namespace AppBundle\Controller\API;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use AppBundle\Entity\User;

/**
 * @Route(name="api_user_")
 */
class UserController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/users", name="list")
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

}