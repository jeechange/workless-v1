<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StandardApply
 */
class StandardApply
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
    private $stardId;

    /**
     * @var integer
     */
    private $auditor;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $applyTime;

    /**
     * @var string
     */
    private $memo;

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
     * @return StandardApply
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
     * Set stardId
     *
     * @param integer $stardId
     * @return StandardApply
     */
    public function setStardId($stardId)
    {
        $this->stardId = $stardId;

        return $this;
    }

    /**
     * Get stardId
     *
     * @return integer 
     */
    public function getStardId()
    {
        return $this->stardId;
    }

    /**
     * Set auditor
     *
     * @param integer $auditor
     * @return StandardApply
     */
    public function setAuditor($auditor)
    {
        $this->auditor = $auditor;

        return $this;
    }

    /**
     * Get auditor
     *
     * @return integer 
     */
    public function getAuditor()
    {
        return $this->auditor;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return StandardApply
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
     * Set applyTime
     *
     * @param \DateTime $applyTime
     * @return StandardApply
     */
    public function setApplyTime($applyTime)
    {
        $this->applyTime = $applyTime;

        return $this;
    }

    /**
     * Get applyTime
     *
     * @return \DateTime 
     */
    public function getApplyTime()
    {
        return $this->applyTime;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return StandardApply
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string 
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return StandardApply
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
     * @var integer
     */
    private $sid;


    /**
     * Set sid
     *
     * @param integer $sid
     * @return StandardApply
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
}
