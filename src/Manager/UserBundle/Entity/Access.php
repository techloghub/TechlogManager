<?php

namespace Manager\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 角色表
 *
 * @author wukai
 *
 * @ORM\Entity
 * @ORM\Table(name="manager_access")
 */
class Access
{
    /**
     * @ORM\ID
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="role_id", type="integer")
     */
    private $roleId;

    /**
     * @ORM\Column(name="route_id", type="integer")
     */
    private $routeId;

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
     * Set roleId
     *
     * @param integer $roleId
     * @return Access
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    
        return $this;
    }

    /**
     * Get roleId
     *
     * @return integer 
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set routeId
     *
     * @param integer $routeId
     * @return Access
     */
    public function setRouteId($routeId)
    {
        $this->routeId = $routeId;
    
        return $this;
    }

    /**
     * Get routeId
     *
     * @return integer 
     */
    public function getRouteId()
    {
        return $this->routeId;
    }
}