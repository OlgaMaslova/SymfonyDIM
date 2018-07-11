<?php

namespace AppBundle\Serializer\Handler;

use AppBundle\Entity\Show;
use AppBundle\File\FileUploader;
use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonDeserializationVisitor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


use JMS\Serializer\Handler\SubscribingHandlerInterface;

class ShowHandler implements SubscribingHandlerInterface
{
    private $doctrine;

    private $tokenStorage;

    public function __construct(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage)
    {
        $this->doctrine = $doctrine;
        $this->tokenStorage = $tokenStorage;
    }


    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'method' => 'deserialize',
                'type' => 'AppBundle\Entity\Show',
                'format' => 'json'
            ],
        ];
    }

    public function deserialize (JsonDeserializationVisitor $visitor, $data) {

        $show = new Show();
        $show->setName($data['name']);
        $show->setAbstract($data['abstract']);
        $show->setCountry($data['country']);
        $show->setReleaseDate(new \DateTime($data['releaseDate']));

        $em = $this->doctrine->getManager();

        if (!$category = $em->getRepository('AppBundle:Category')->findOneById($data['category']['id'])) {
            throw new \LogicException('Category does not exist');
        }

        $show->setCategory($category);

        $user = $this->tokenStorage->getToken()->getUser();
        $show->setAuthor($user);
        dump($show);die;
    }
}