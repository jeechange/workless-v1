<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetOthers
 */
class TargetOthers
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $tdId;

    /**
     * @var string
     */
    private $score;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var string
     */
    private $status;


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
     * Set userId
     *
     * @param integer $userId
     * @return TargetOthers
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
     * Set tdId
     *
     * @param integer $tdId
     * @return TargetOthers
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
     * Set score
     *
     * @param string $score
     * @return TargetOthers
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return string 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TargetOthers
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
     * Set status
     *
     * @param string $status
     * @return TargetOthers
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @var string
     */
    private $content;


    /**
     * Set content
     *
     * @param string $content
     * @return TargetOthers
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
     * @var integer
     */
    private $mId;


    /**
     * Set mId
     *
     * @param integer $mId
     * @return TargetOthers
     */
    public function setMId($mId)
    {
        $this->mId = $mId;

        return $this;
    }

    /**
     * Get mId
     *
     * @return integer 
     */
    public function getMId()
    {
        return $this->mId;
    }
}
