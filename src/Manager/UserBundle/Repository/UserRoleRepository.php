<?php

namespace Manager\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRoleRepository extends EntityRepository
{
    public function getUserByName($username)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->executeQuery("set names utf8");
        $dql = "SELECT p FROM ManagerUserBundle:UserRole p where p.username=:username";
        $query = $em->createQuery($dql);
        $query->setParameter("username", $username);
        $result = $query->getOneOrNullResult();
        return $result;
    }
    
    public function getUserList()
    {
        $em = $this->getEntityManager();
        $em->getConnection()->executeQuery("set names utf8");
        $dql = "SELECT p FROM ManagerUserBundle:UserRole p";
        $query = $em->createQuery($dql);
        $result = $query->getResult();
        return $result;
    }
}
