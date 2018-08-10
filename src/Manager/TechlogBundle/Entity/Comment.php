<?php

namespace Manager\TechlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Manager\TechlogBundle\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="comment_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $commentId;

    /**
     * @var string
     *
     * @ORM\Column(name="inserttime", type="string", nullable=false)
     */
    private $inserttime;

    /**
     * @var integer
     *
     * @ORM\Column(name="article_id", type="integer", nullable=true)
     */
    private $articleId;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=32, nullable=false)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="qq", type="integer", nullable=false)
     */
    private $qq;

    /**
     * @var integer
     *
     * @ORM\Column(name="reply", type="integer", nullable=true)
     */
    private $reply;

    /**
     * @var boolean
     *
     * @ORM\Column(name="online", type="boolean", nullable=false)
     */
    private $online;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=32, nullable=false)
     */
    private $nickname;

    /**
     * @var integer
     *
     * @ORM\Column(name="floor", type="integer", nullable=false)
     */
    private $floor;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15, nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="updatetime", type="string", nullable=false)
     */
    private $updatetime;



    /**
     * Get commentId
     *
     * @return integer 
     */
    public function getCommentId()
    {
        return $this->commentId;
    }

    /**
     * Set inserttime
     *
     * @param string $inserttime
     * @return Comment
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
     * Set articleId
     *
     * @param integer $articleId
     * @return Comment
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
     * Set email
     *
     * @param string $email
     * @return Comment
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set qq
     *
     * @param integer $qq
     * @return Comment
     */
    public function setQq($qq)
    {
        $this->qq = $qq;
    
        return $this;
    }

    /**
     * Get qq
     *
     * @return integer 
     */
    public function getQq()
    {
        return $this->qq;
    }

    /**
     * Set reply
     *
     * @param integer $reply
     * @return Comment
     */
    public function setReply($reply)
    {
        $this->reply = $reply;
    
        return $this;
    }

    /**
     * Get reply
     *
     * @return integer 
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * Set online
     *
     * @param boolean $online
     * @return Comment
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
     * Set nickname
     *
     * @param string $nickname
     * @return Comment
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    
        return $this;
    }

    /**
     * Get nickname
     *
     * @return string 
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set floor
     *
     * @param integer $floor
     * @return Comment
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;
    
        return $this;
    }

    /**
     * Get floor
     *
     * @return integer 
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Comment
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
     * Set ip
     *
     * @param string $ip
     * @return Comment
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set updatetime
     *
     * @param string $updatetime
     * @return Comment
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
}
