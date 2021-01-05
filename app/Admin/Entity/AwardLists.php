<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AwardLists
 */
class AwardLists
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
    private $userId;

    /**
     * @var integer
     */
    private $fromId;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $memo;

    /**
     * @var string
     */
    private $award;

    /**
     * @var string
     */
    private $prize;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $sysmemo;

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
     * Set sid
     *
     * @param integer $sid
     * @return AwardLists
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
     * Set userId
     *
     * @param integer $userId
     * @return AwardLists
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
     * Set fromId
     *
     * @param integer $fromId
     * @return AwardLists
     */
    public function setFromId($fromId)
    {
        $this->fromId = $fromId;

        return $this;
    }

    /**
     * Get fromId
     *
     * @return integer 
     */
    public function getFromId()
    {
        return $this->fromId;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return AwardLists
     */
    public function setNames($names)
    {
        $this->names = $names;

        return $this;
    }

    /**
     * Get names
     *
     * @return string 
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return AwardLists
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
     * Set award
     *
     * @param string $award
     * @return AwardLists
     */
    public function setAward($award)
    {
        $this->award = $award;

        return $this;
    }

    /**
     * Get award
     *
     * @return string 
     */
    public function getAward()
    {
        return $this->award;
    }

    /**
     * Set prize
     *
     * @param string $prize
     * @return AwardLists
     */
    public function setPrize($prize)
    {
        $this->prize = $prize;

        return $this;
    }

    /**
     * Get prize
     *
     * @return string 
     */
    public function getPrize()
    {
        return $this->prize;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return AwardLists
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set sysmemo
     *
     * @param string $sysmemo
     * @return AwardLists
     */
    public function setSysmemo($sysmemo)
    {
        $this->sysmemo = $sysmemo;

        return $this;
    }

    /**
     * Get sysmemo
     *
     * @return string 
     */
    public function getSysmemo()
    {
        return $this->sysmemo;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return AwardLists
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
