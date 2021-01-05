<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dynamic
 */
class Dynamic
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $sid;

    /**
     * @var integer
     */
    private $did;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $replyId;

    /**
     * @var string
     */
    private $ats;

    /**
     * @var string
     */
    private $relateUser;

    /**
     * @var string
     */
    private $unreads;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $linkTo;

    /**
     * @var string
     */
    private $likes;

    /**
     * @var integer
     */
    private $types;


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
     * Set sid
     *
     * @param integer $sid
     * @return Dynamic
     */
    public function setSid($sid)
    {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid
     *
     * @return integer 
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set did
     *
     * @param integer $did
     * @return Dynamic
     */
    public function setDid($did)
    {
        $this->did = $did;

        return $this;
    }

    /**
     * Get did
     *
     * @return integer 
     */
    public function getDid()
    {
        return $this->did;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Dynamic
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set replyId
     *
     * @param integer $replyId
     * @return Dynamic
     */
    public function setReplyId($replyId)
    {
        $this->replyId = $replyId;

        return $this;
    }

    /**
     * Get replyId
     *
     * @return integer 
     */
    public function getReplyId()
    {
        return $this->replyId;
    }

    /**
     * Set ats
     *
     * @param string $ats
     * @return Dynamic
     */
    public function setAts($ats)
    {
        $this->ats = $ats;

        return $this;
    }

    /**
     * Get ats
     *
     * @return string 
     */
    public function getAts()
    {
        return $this->ats;
    }

    /**
     * Set relateUser
     *
     * @param string $relateUser
     * @return Dynamic
     */
    public function setRelateUser($relateUser)
    {
        $this->relateUser = $relateUser;

        return $this;
    }

    /**
     * Get relateUser
     *
     * @return string 
     */
    public function getRelateUser()
    {
        return $this->relateUser;
    }

    /**
     * Set unreads
     *
     * @param string $unreads
     * @return Dynamic
     */
    public function setUnreads($unreads)
    {
        $this->unreads = $unreads;

        return $this;
    }

    /**
     * Get unreads
     *
     * @return string 
     */
    public function getUnreads()
    {
        return $this->unreads;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Dynamic
     */
    public function setAddTime($addTime)
    {
        $this->addTime = $addTime;

        return $this;
    }

    /**
     * Get addTime
     *
     * @return \DateTime 
     */
    public function getAddTime()
    {
        return $this->addTime;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Dynamic
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
     * Set linkTo
     *
     * @param string $linkTo
     * @return Dynamic
     */
    public function setLinkTo($linkTo)
    {
        $this->linkTo = $linkTo;

        return $this;
    }

    /**
     * Get linkTo
     *
     * @return string 
     */
    public function getLinkTo()
    {
        return $this->linkTo;
    }

    /**
     * Set likes
     *
     * @param string $likes
     * @return Dynamic
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return string 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return Dynamic
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return integer 
     */
    public function getTypes()
    {
        return $this->types;
    }
}
