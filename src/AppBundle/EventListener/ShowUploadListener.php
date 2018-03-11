<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Show;
use AppBundle\File\FileUploader;
use Symfony\Component\HttpFoundation\File\File;


class ShowUploadListener
{
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    private function uploadFile($entity)
    {
        // upload only works for Show entities
        if (!$entity instanceof Show) {
            return;
        }

        $file = $entity->getMainPicture();

        // only upload new files
        if ($file instanceof UploadedFile) {
            $fileName = $this->fileUploader->upload($file, $entity->getCategory()->getName());
            $entity->setMainPicture($fileName);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Show) {
            return;
        }
        //save the mainPicture filename in mainPicture property for DB
        if ($fileName = $entity->getMainPicture()) {
            $entity->setMainPictureFileName($fileName);
            $entity->setMainPicture(new File($this->fileUploader->getUploadDirectoryPath().'/'.$fileName));
        }
    }
}