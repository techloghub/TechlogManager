<?php

namespace Manager\ChargeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="Manager\ChargeBundle\Repository\AccountRepository")
 */
class Account
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
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="esid", type="string", length=50, nullable=false)
     */
    private $esid;

    /**
     * @var string
     *
     * @ORM\Column(name="inserttime", type="string", nullable=false)
     */
    private $inserttime;

    /**
     * @var string
     *
     * @ORM\Column(name="updatetime", type="string", nullable=false)
     */
    private $updatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=20, nullable=false)
     */
    private $currency;

    /**
     * @var integer
     *
     * @ORM\Column(name="orderno", type="integer", nullable=false)
     */
    private $orderno;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=10, nullable=false)
     */
    private $category;

    /**
     * @var float
     *
     * @ORM\Column(name="money", type="float", nullable=false)
     */
    private $money;

    /**
     * @var string
     *
     * @ORM\Column(name="cardno", type="string", length=20, nullable=false)
     */
    private $cardno;



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
     * @return Account
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
     * Set esid
     *
     * @param string $esid
     * @return Account
     */
    public function setEsid($esid)
    {
        $this->esid = $esid;
    
        return $this;
    }

    /**
     * Get esid
     *
     * @return string 
     */
    public function getEsid()
    {
        return $this->esid;
    }

    /**
     * Set inserttime
     *
     * @param string $inserttime
     * @return Account
     */
    public function setInserttime($inserttime)
    {
        $this->inserttime = $inserttime;
    
        return $this;
    }

    /**
     * Get inserttime
     *
     * @return string 
     */
    public function getInserttime()
    {
        return $this->inserttime;
    }

    /**
     * Set updatetime
     *
     * @param string $updatetime
     * @return Account
     */
    public function setUpdatetime($updatetime)
    {
        $this->updatetime = $updatetime;
    
        return $this;
    }

    /**
     * Get updatetime
     *
     * @return string 
     */
    public function getUpdatetime()
    {
        return $this->updatetime;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Account
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    
        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set orderno
     *
     * @param integer $orderno
     * @return Account
     */
    public function setOrderno($orderno)
    {
        $this->orderno = $orderno;
    
        return $this;
    }

    /**
     * Get orderno
     *
     * @return integer 
     */
    public function getOrderno()
    {
        return $this->orderno;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Account
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set money
     *
     * @param float $money
     * @return Account
     */
    public function setMoney($money)
    {
        $this->money = $money;
    
        return $this;
    }

    /**
     * Get money
     *
     * @return float 
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Set cardno
     *
     * @param string $cardno
     * @return Account
     */
    public function setCardno($cardno)
    {
        $this->cardno = $cardno;
    
        return $this;
    }

    /**
     * Get cardno
     *
     * @return string 
     */
    public function getCardno()
    {
        return $this->cardno;
    }
}
