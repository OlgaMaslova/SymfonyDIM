<?php

namespace AppBundle\Serializer\Listener;

use AppBundle\Entity\Show;
use AppBundle\File\FileUploader;
use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ShowListener implements EventSubscriberInterface
{
    private $doctrine;

    private $tokenStorage;

    public function __construct(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage)
    {
        $this->doctrine = $doctrine;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns the events to which this class has subscribed.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => Events::PRE_DESERIALIZE,
                'method' => 'preDeserialize',
                'class' => 'AppBundle\\Entity\\Show',
                'format' => 'json'
            ],
        ];
    }

    public function preDeserialize(PreDeserializeEvent $event) {

    }
}