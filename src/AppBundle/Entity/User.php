<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ORM\Entity
 * @ORM\Table
 *
 * @UniqueEntity("email")
 * @JMS\ExclusionPolicy("all")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @JMS\Expose
     * @JMS\Groups({"user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     *
     * @JMS\Expose
     * @JMS\Groups({"user", "show", "show_create", "show_update"})
     */
    private $fullname;

    /**
     * @ORM\Column(type="json_array")
     *
     * @JMS\Expose
     * @JMS\Groups({"user_create", "user_update"})
     * @JMS\Type("string")
     */
    private $roles;

    /**
     * @ORM\Column
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     * @JMS\Groups({"user_create"})
     */
    private $password;

    /**
     * @ORM\Column
     *
     * @Assert\Email
     *
     * @JMS\Expose
     * @JMS\Groups({"user"})
     */
    private $email;
    /**
     * @ORM\OneToMany(targetEntity="Show", mappedBy="author")
     */
    private $shows;

    public function __construct()
    {
        $this->shows = new ArrayCollection();
    }

    public function update(User $user)
    {
        $this->email = $user->getUsername();
        $this->fullname = $user->getFullname();
        $this->roles = $user->getRoles();
    }

    public function addShow(Show $show)
    {
        if (!$this->shows->contains($show)) {
            $this->shows->add($show);
        }
    }

    public function removeShow(Show $show)
    {
        if ($this->shows->contains($show)) {
            $this->shows->remove($show);
        }
    }

    public function getShows()
    {
        return $this->shows;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }
    /**
     * @param mixed $fullname
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getPassword()
    {
       return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function setUsername($email)
    {
        $this->email = $email;
    }

    public function eraseCredentials()
    {
    }
}