<?php

namespace Manager\TechlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Manager\TechlogBundle\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @var integer
     *
     * @ORM\Column(name="article_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $articleId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=false)
     */
    private $title;

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
     * @ORM\Column(name="indexs", type="string", length=2048, nullable=true)
     */
    private $indexs;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer", nullable=false)
     */
    private $categoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="title_desc", type="string", length=100, nullable=false)
     */
    private $titleDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="draft", type="text", nullable=false)
     */
    private $draft;

    /**
     * @var integer
     *
     * @ORM\Column(name="access_count", type="integer", nullable=false)
     */
    private $accessCount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="online", type="boolean", nullable=false)
     */
    private $online;

    /**
     * @var integer
     *
     * @ORM\Column(name="comment_count", type="integer", nullable=false)
     */
    private $commentCount;



    /**
     * Get articleId
     *
     * @return integer 
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set inserttime
     *
     * @param string $inserttime
     * @return Article
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
     * @return Article
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
     * Set indexs
     *
     * @param string $indexs
     * @return Article
     */
    public function setIndexs($indexs)
    {
        $this->indexs = $indexs;
    
        return $this;
    }

    /**
     * Get indexs
     *
     * @return string 
     */
    public function getIndexs()
    {
        return $this->indexs;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return Article
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    
        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set titleDesc
     *
     * @param string $titleDesc
     * @return Article
     */
    public function setTitleDesc($titleDesc)
    {
        $this->titleDesc = $titleDesc;
    
        return $this;
    }

    /**
     * Get titleDesc
     *
     * @return string 
     */
    public function getTitleDesc()
    {
        return $this->titleDesc;
    }

    /**
     * Set draft
     *
     * @param string $draft
     * @return Article
     */
    public function setDraft($draft)
    {
        $this->draft = $draft;
    
        return $this;
    }

    /**
     * Get draft
     *
     * @return string 
     */
    public function getDraft()
    {
        return $this->draft;
    }

    /**
     * Set accessCount
     *
     * @param integer $accessCount
     * @return Article
     */
    public function setAccessCount($accessCount)
    {
        $this->accessCount = $accessCount;
    
        return $this;
    }

    /**
     * Get accessCount
     *
     * @return integer 
     */
    public function getAccessCount()
    {
        return $this->accessCount;
    }

    /**
     * Set online
     *
     * @param boolean $online
     * @return Article
     */
    public function setOnline($online)
    {
        $this->online = $online;
    
        return $this;
    }

    /**
     * Get online
     *
     * @return boolean 
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * Set commentCount
     *
     * @param integer $commentCount
     * @return Article
     */
    public function setCommentCount($commentCount)
    {
        $this->commentCount = $commentCount;
    
        return $this;
    }

    /**
     * Get commentCount
     *
     * @return integer 
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    }
}
