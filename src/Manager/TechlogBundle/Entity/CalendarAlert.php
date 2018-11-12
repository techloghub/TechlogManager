<?php

namespace Manager\TechlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CalendarAlert
 *
 * @ORM\Table(name="calendar_alert")
 * @ORM\Entity
 */
class CalendarAlert
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
     * @var \DateTime
     *
     * @ORM\Column(name="insert_time", type="datetime", nullable=false)
     */
    private $insertTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_time", type="datetime", nullable=false)
     */
    private $updateTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=false)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=false)
     */
    private $endTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="alert_time", type="datetime", nullable=false)
     */
    private $alertTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="lunar", type="integer", nullable=false)
     */
    private $lunar;

    /**
     * @var integer
     *
     * @ORM\Column(name="cycle_type", type="integer", nullable=false)
     */
    private $cycleType;

    /**
     * @var integer
     *
     * @ORM\Column(name="period", type="integer", nullable=false)
     */
    private $period;

    /**
     * @var integer
     *
     * @ORM\Column(name="category", type="integer", nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=2048, nullable=false)
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
     * @return CalendarAlert
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
     * @param \DateTime $insertTime
     * @return CalendarAlert
     */
    public function setInsertTime($insertTime)
    {
        $this->insertTime = $insertTime;
    
        return $this;
    }

    /**
     * Get insertTime
     *
     * @return \DateTime 
     */
    public function getInsertTime()
    {
        return $this->insertTime;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     * @return CalendarAlert
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
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return CalendarAlert
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    
        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return CalendarAlert
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    
        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set alertTime
     *
     * @param \DateTime $alertTime
     * @return CalendarAlert
     */
    public function setAlertTime($alertTime)
    {
        $this->alertTime = $alertTime;
    
        return $this;
    }

    /**
     * Get alertTime
     *
     * @return \DateTime 
     */
    public function getAlertTime()
    {
        return $this->alertTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return CalendarAlert
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
     * Set lunar
     *
     * @param integer $lunar
     * @return CalendarAlert
     */
    public function setLunar($lunar)
    {
        $this->lunar = $lunar;
    
        return $this;
    }

    /**
     * Get lunar
     *
     * @return integer 
     */
    public function getLunar()
    {
        return $this->lunar;
    }

    /**
     * Set cycleType
     *
     * @param integer $cycleType
     * @return CalendarAlert
     */
    public function setCycleType($cycleType)
    {
        $this->cycleType = $cycleType;
    
        return $this;
    }

    /**
     * Get cycleType
     *
     * @return integer 
     */
    public function getCycleType()
    {
        return $this->cycleType;
    }

    /**
     * Set period
     *
     * @param integer $period
     * @return CalendarAlert
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    
        return $this;
    }

    /**
     * Get period
     *
     * @return integer 
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set category
     *
     * @param integer $category
     * @return CalendarAlert
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return integer 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set remark
     *
     * @param string $remark
     * @return CalendarAlert
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