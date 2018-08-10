<?php

namespace Manager\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * 用户与角色信息
 *
 * @author tianye-s
 *
 * @ORM\Entity(repositoryClass="Manager\UserBundle\Repository\UserRoleRepository")
 * @ORM\Table(name="manager_user_role")
 */
class UserRole implements UserInterface, EquatableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;
    /**
     * @ORM\Column(name="username", type="string")
     */
    private $username;

    public function getRoles()
    {
        return array("ROLE_USER");
    }

    public function getPassword()
    {
        return "";
    }

    public function getSalt()
    {
        return "";
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof UserRole) {
            return false;
        }
        if ($this->getRoles() !== $user->getRoles()) {
            return false;
        }

        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }
        return true;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return UserRole
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }
}
