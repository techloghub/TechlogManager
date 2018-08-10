<?php

namespace Manager\TechlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskList
 *
 * @ORM\Table(name="task_list")
 * @ORM\Entity(repositoryClass="Manager\TechlogBundle\Repository\TaskListRepository")
 */
class TaskList
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string 
     *
     * @ORM\Column(name="insert_time", type="string", nullable=false)
     */
    private $insertTime;

    /**
     * @var string
     *
     * @ORM\Column(name="update_time", type="string", nullable=false)
     */
    private $updateTime;

    /**
     * @var string
     *
     * @ORM\Column(name="finish_time", type="string", nullable=false)
     */
    private $finishTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=256, nullable=false)
     */
    private $remark;



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
     * @return TaskList
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
     * Set insertTime
     *
     * @param string $insertTime
     * @return TaskList
     */
    public function setInsertTime($insertTime)
    {
        $this->insertTime = $insertTime;
    
        return $this;
    }

    /**
     * Get insertTime
     *
     * @return string 
     */
    public function getInsertTime()
    {
        return $this->insertTime;
    }

    /**
     * Set updateTime
     *
     * @param string $updateTime
     * @return TaskList
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    
        return $this;
    }

    /**
     * Get updateTime
     *
     * @return string 
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set finishTime
     *
     * @param string $finishTime
     * @return TaskList
     */
    public function setFinishTime($finishTime)
    {
        $this->finishTime = $finishTime;
    
        return $this;
    }

    /**
     * Get finishTime
     *
     * @return string 
     */
    public function getFinishTime()
    {
        return $this->finishTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return TaskList
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set remark
     *
     * @param string $remark
     * @return TaskList
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
}