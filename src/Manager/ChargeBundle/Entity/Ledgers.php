<?php

namespace Manager\ChargeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ledgers
 *
 * @ORM\Table(name="ledgers")
 * @ORM\Entity(repositoryClass="Manager\ChargeBundle\Repository\LedgersRepository")
 */
class Ledgers
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
     * @ORM\Column(name="esid", type="string", length=50, nullable=false)
     */
    private $esid;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="inserttime", type="string", nullable=false)
     */
    private $inserttime;

    /**
     * @var integer
     *
     * @ORM\Column(name="recType", type="integer", nullable=false)
     */
    private $rectype;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=20, nullable=false)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=50, nullable=false)
     */
    private $comment;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="fromAcc", type="string", length=50, nullable=false)
     */
    private $fromacc;

    /**
     * @var string
     *
     * @ORM\Column(name="toAcc", type="string", length=50, nullable=false)
     */
    private $toacc;

    /**
     * @var float
     *
     * @ORM\Column(name="money", type="float", nullable=false)
     */
    private $money;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=20, nullable=false)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=20, nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="subcategory", type="string", length=20, nullable=false)
     */
    private $subcategory;



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
     * Set esid
     *
     * @param string $esid
     * @return Ledgers
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
     * Set date
     *
     * @param string $date
     * @return Ledgers
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return string 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set inserttime
     *
     * @param string $inserttime
     * @return Ledgers
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
     * Set rectype
     *
     * @param integer $rectype
     * @return Ledgers
     */
    public function setRectype($rectype)
    {
        $this->rectype = $rectype;
    
        return $this;
    }

    /**
     * Get rectype
     *
     * @return integer 
     */
    public function getRectype()
    {
        return $this->rectype;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Ledgers
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Ledgers
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Ledgers
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set fromacc
     *
     * @param string $fromacc
     * @return Ledgers
     */
    public function setFromacc($fromacc)
    {
        $this->fromacc = $fromacc;
    
        return $this;
    }

    /**
     * Get fromacc
     *
     * @return string 
     */
    public function getFromacc()
    {
        return $this->fromacc;
    }

    /**
     * Set toacc
     *
     * @param string $toacc
     * @return Ledgers
     */
    public function setToacc($toacc)
    {
        $this->toacc = $toacc;
    
        return $this;
    }

    /**
     * Get toacc
     *
     * @return string 
     */
    public function getToacc()
    {
        return $this->toacc;
    }

    /**
     * Set money
     *
     * @param float $money
     * @return Ledgers
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
     * Set currency
     *
     * @param string $currency
     * @return Ledgers
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
     * Set category
     *
     * @param string $category
     * @return Ledgers
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
     * Set subcategory
     *
     * @param string $subcategory
     * @return Ledgers
     */
    public function setSubcategory($subcategory)
    {
        $this->subcategory = $subcategory;
    
        return $this;
    }

    /**
     * Get subcategory
     *
     * @return string 
     */
    public function getSubcategory()
    {
        return $this->subcategory;
    }
}
