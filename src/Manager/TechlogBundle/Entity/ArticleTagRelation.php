<?php

namespace Manager\TechlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleTagRelation
 *
 * @ORM\Table(name="article_tag_relation")
 * @ORM\Entity(repositoryClass="Manager\TechlogBundle\Repository\RelationRepository")
 */
class ArticleTagRelation
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
     * @var integer
     *
     * @ORM\Column(name="article_id", type="integer", nullable=false)
     */
    private $articleId;

    /**
     * @var integer
     *
     * @ORM\Column(name="tag_id", type="integer", nullable=false)
     */
    private $tagId;

    /**
     * @var string
     *
     * @ORM\Column(name="inserttime", type="string", nullable=false)
     */
    private $inserttime;



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
     * Set articleId
     *
     * @param integer $articleId
     * @return ArticleTagRelation
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
    
        return $this;
    }

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
     * Set tagId
     *
     * @param integer $tagId
     * @return ArticleTagRelation
     */
    public function setTagId($tagId)
    {
        $this->tagId = $tagId;
    
        return $this;
    }

    /**
     * Get tagId
     *
     * @return integer 
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * Set inserttime
     *
     * @param string $inserttime
     * @return ArticleTagRelation
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
}