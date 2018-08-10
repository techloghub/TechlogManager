<?php

namespace Manager\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 路由表
 *
 * @author wukai
 * @ORM\Entity(repositoryClass="Manager\UserBundle\Repository\PathRepository")
 * @ORM\Table(name="manager_path")
 */
class Path
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(name="menu_1", type="integer")
     */
    private $firstMenu;

    /**
     * @ORM\Column(name="menu_2", type="integer")
     */
    private $secondMenu;

    /**
     * @ORM\Column(type="string")
     */
    private $route;

    /**
     * @ORM\Column(type="string")
     */
    private $remark;

    /**
     * @ORM\Column(type="string")
     */
    private $operator;

    /**
     * @ORM\Column(name="update_time", type="datetime")
     */
    private $updateTime;

    /**
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

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
     * Set name
     *
     * @param string $name
     * @return Block
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstMenu
     *
     * @param integer $firstMenu
     * @return Block
     */
    public function setFirstMenu($firstMenu)
    {
        $this->firstMenu = $firstMenu;
    
        return $this;
    }

    /**
     * Get firstMenu
     *
     * @return integer 
     */
    public function getFirstMenu()
    {
        return $this->firstMenu;
    }

    /**
     * Set secondMenu
     *
     * @param integer $secondMenu
     * @return Block
     */
    public function setSecondMenu($secondMenu)
    {
        $this->secondMenu = $secondMenu;
    
        return $this;
    }

    /**
     * Get secondMenu
     *
     * @return integer 
     */
    public function getSecondMenu()
    {
        return $this->secondMenu;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Block
     */
    public function setRoute($route)
    {
        $this->route = $route;
    
        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set remark
     *
     * @param string $remark
     * @return Block
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    
        return $this;
    }

    /**
     * Get remark
     *
     * @return string 
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set operator
     *
     * @param string $operator
     * @return Block
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    
        return $this;
    }

    /**
     * Get operator
     *
     * @return string 
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     * @return Block
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    
        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime 
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Block
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    
        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }
}
