<?php

namespace Manager\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OptLog
 *
 * @ORM\Table(name="opt_log")
 * @ORM\Entity(repositoryClass="Manager\UserBundle\Repository\OptLogRepository")
 */
class OptLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="operator", type="string", length=64, nullable=false)
     */
    private $operator;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=128, nullable=false)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="create_time", type="string", nullable=false)
     */
    private $createTime;

    public function __construct(){
        $this->setCreateTime(date('Y-m-d H:i:s'));
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
     * Set operator
     *
     * @param string $operator
     * @return OptLog
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
     * Set route
     *
     * @param string $route
     * @return OptLog
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
     * Set content
     *
     * @param string $content
     * @return OptLog
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createTime
     *
     * @param string $createTime
     * @return OptLog
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return string
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }
}
