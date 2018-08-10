<?php
namespace Manager\UserBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Manager\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserRoleProvider implements UserProviderInterface
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function loadUserByUsername($username)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $user = $em->getRepository('ManagerUserBundle:User')->getUserByName($username);
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist(db error)!', $username));
        }
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Manager\UserBundle\Security\User\UserRole';
    }
}