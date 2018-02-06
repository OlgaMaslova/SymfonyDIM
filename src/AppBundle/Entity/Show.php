<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="s_show")
 */

class Show
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column
     * @Assert\NotBlank(message = "Please provide name for the show", groups={"create", "update"})
    */
    private $name;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Please provide abstract for the show", groups={"create", "update"})
     */
    private $abstract;
    /**
     * @ORM\Column
     * @Assert\NotBlank(message = "Please provide country for the show", groups={"create", "update"})
     */
    private $country;
    /**
     * @ORM\Column
     * @Assert\NotBlank(message = "Please provide author for the show", groups={"create", "update"})
     */
    private $author;
    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message = "Please provide release date for the show", groups={"create", "update"})
     */
    private $releaseDate;
    /**
     * @ORM\Column
     * @Assert\Image(minHeight=300, minWidth=750, groups={"create"})
     *
     */
    private $mainPicture;

    private $tmpPicture;
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\NotBlank(message = "Please provide category for the show", groups={"create", "update"})
     */
    private $category;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param mixed $abstract
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * @param mixed $releaseDate
     */
    public function setReleaseDate(\DateTime $releaseDate)
    {
        $this->releaseDate = $releaseDate;
    }

    /**
     * @return mixed
     */
    public function getMainPicture()
    {
        return $this->mainPicture;
    }

    /**
     * @param mixed $mainPicture
     */
    public function setMainPicture($mainPicture)
    {
        $this->mainPicture = $mainPicture;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getTmpPicture()
    {
        return $this->tmpPicture;
    }

    /**
     * @param mixed $tmpPicture
     */
    public function setTmpPicture($tmpPicture)
    {
        $this->tmpPicture = $tmpPicture;
    }



}