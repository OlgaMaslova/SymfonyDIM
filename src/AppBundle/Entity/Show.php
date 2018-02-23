<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ShowRepository")
 * @ORM\Table(name="s_show")
 * @JMS\ExclusionPolicy("all")
 */

class Show implements \Serializable
{
    const DATA_SOURCE_OMDB =  'OMDB';
    const DATA_SOURCE_DB =  'In local database';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Expose
     * @JMS\Groups({"show"})
     */
    private $id;
    /**
     * @ORM\Column
     * @Assert\NotBlank(message = "Please provide name for the show", groups={"create", "update"})
     * @JMS\Expose
     * @JMS\Groups({"show", "show_create", "show_update"})
    */
    private $name;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Please provide abstract for the show", groups={"create", "update"})
     * @JMS\Expose
     * @JMS\Groups({"show", "show_create", "show_update"})
     */
    private $abstract;
    /**
     * @ORM\Column
     * @Assert\NotBlank(message = "Please provide country for the show", groups={"create", "update"})
     * @JMS\Expose
     * @JMS\Groups({"show", "show_create", "show_update"})
     */
    private $country;
    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="shows", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Groups({"show", "show_create"})
     */
    private $author;
    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message = "Please provide release date for the show", groups={"create", "update"})
     * @JMS\Expose
     * @JMS\Groups({"show", "show_create", "show_update"})
     */
    private $releaseDate;
    /**
     * @ORM\Column
     * @Assert\NotBlank(message = "Please provide a picture", groups={"create", "update" })
     * @Assert\Image(minHeight=300, minWidth=750, groups={"create", "update"})
     */
    private $mainPicture;

    private $mainPictureFileName;

    /**
     * @ORM\ManyToOne(targetEntity="Category", cascade={"persist"})
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\NotBlank(message = "Please provide category for the show", groups={"create", "update"})
     * @JMS\Expose
     * @JMS\Groups({"show_create", "show_update"})
     */
    private $category;

    /**
     * @ORM\Column(options = {"default" = "In local database"})
     * @JMS\Expose
     */
    private $dbSource;

    /**
     * Update a show
     * @param a show with new variables
     */
    public function update(Show $show)
    {
        $this->name = $show->getName();
        $this->abstract = $show->getAbstract();
        $this->releaseDate = $show->getReleaseDate();
        $this->category = $show->getCategory();
        $this->country = $show->getCountry();
    }

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
    public function setAuthor(User $author)
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
    public function getMainPictureFileName()
    {
        return $this->mainPictureFileName;
    }

    /**
     * @param mixed $mainPictureFileName
     */
    public function setMainPictureFileName($mainPictureFileName)
    {
        $this->mainPictureFileName = $mainPictureFileName;
    }

    /**
     * @return mixed
     */
    public function getDbSource()
    {
        return $this->dbSource;
    }

    /**
     * @param mixed $dbSource
     */
    public function setDbSource($dbSource)
    {
        $this->dbSource = $dbSource;
    }

    public function serialize()
    {
        // TODO: Implement serialize() method.
    }

    public function unserialize($serialized)
    {
        // TODO: Implement unserialize() method.
    }


}