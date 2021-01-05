<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WelfareReward
 */
class WelfareReward
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
    private $fromUser;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $memo;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $useTime;


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
     * @return WelfareReward
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
     * @return WelfareReward
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
     * Set fromUser
     *
     * @param integer $fromUser
     * @return WelfareReward
     */
    public function setFromUser($fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return integer 
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return WelfareReward
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
     * @return WelfareReward
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
     * @param integer $status
     * @return WelfareReward
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
     * Set types
     *
     * @param integer $types
     * @return WelfareReward
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

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return WelfareReward
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
     * Set useTime
     *
     * @param \DateTime $useTime
     * @return WelfareReward
     */
    public function setUseTime($useTime)
    {
        $this->useTime = $useTime;

        return $this;
    }

    /**
     * Get useTime
     *
     * @return \DateTime 
     */
    public function getUseTime()
    {
        return $this->useTime;
    }
    /**
     * @var integer
     */
    private $wvId;


    /**
     * Set wvId
     *
     * @param integer $wvId
     * @return WelfareReward
     */
    public function setWvId($wvId)
    {
        $this->wvId = $wvId;

        return $this;
    }

    /**
     * Get wvId
     *
     * @return integer 
     */
    public function getWvId()
    {
        return $this->wvId;
    }
}
