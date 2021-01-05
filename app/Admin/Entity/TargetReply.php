<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetReply
 */
class TargetReply
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $tdId;

    /**
     * @var integer
     */
    private $teId;

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
    private $content;

    /**
     * @var \DateTime
     */
    private $addTime;


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
     * Set tdId
     *
     * @param integer $tdId
     * @return TargetReply
     */
    public function setTdId($tdId)
    {
        $this->tdId = $tdId;

        return $this;
    }

    /**
     * Get tdId
     *
     * @return integer 
     */
    public function getTdId()
    {
        return $this->tdId;
    }

    /**
     * Set teId
     *
     * @param integer $teId
     * @return TargetReply
     */
    public function setTeId($teId)
    {
        $this->teId = $teId;

        return $this;
    }

    /**
     * Get teId
     *
     * @return integer 
     */
    public function getTeId()
    {
        return $this->teId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return TargetReply
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
     * @return TargetReply
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
     * Set content
     *
     * @param string $content
     * @return TargetReply
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TargetReply
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
}
