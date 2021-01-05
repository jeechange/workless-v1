<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetEval
 */
class TargetEval
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
    private $userId;

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
     * @return TargetEval
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
     * Set userId
     *
     * @param integer $userId
     * @return TargetEval
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
     * Set content
     *
     * @param string $content
     * @return TargetEval
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
     * @return TargetEval
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
