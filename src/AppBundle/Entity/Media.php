<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */

class Media {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    private $file;

    /**
     * @ORM\Column
     */
    private $path;

    public function getFile() {
        return $this->file;
    }


    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }


    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }


}