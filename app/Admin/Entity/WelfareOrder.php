<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WelfareOrder
 */
class WelfareOrder
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
    private $snackId;

    /**
     * @var string
     */
    private $acorn;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $autoTime;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var string
     */
    private $orderNo;

    /**
     * @var string
     */
    private $auditorName;


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
     * @return WelfareOrder
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
     * @return WelfareOrder
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
     * Set snackId
     *
     * @param integer $snackId
     * @return WelfareOrder
     */
    public function setSnackId($snackId)
    {
        $this->snackId = $snackId;

        return $this;
    }

    /**
     * Get snackId
     *
     * @return integer 
     */
    public function getSnackId()
    {
        return $this->snackId;
    }

    /**
     * Set acorn
     *
     * @param string $acorn
     * @return WelfareOrder
     */
    public function setAcorn($acorn)
    {
        $this->acorn = $acorn;

        return $this;
    }

    /**
     * Get acorn
     *
     * @return string 
     */
    public function getAcorn()
    {
        return $this->acorn;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return WelfareOrder
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
     * Set autoTime
     *
     * @param \DateTime $autoTime
     * @return WelfareOrder
     */
    public function setAutoTime($autoTime)
    {
        $this->autoTime = $autoTime;

        return $this;
    }

    /**
     * Get autoTime
     *
     * @return \DateTime 
     */
    public function getAutoTime()
    {
        return $this->autoTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return WelfareOrder
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
     * @return WelfareOrder
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
     * Set orderNo
     *
     * @param string $orderNo
     * @return WelfareOrder
     */
    public function setOrderNo($orderNo)
    {
        $this->orderNo = $orderNo;

        return $this;
    }

    /**
     * Get orderNo
     *
     * @return string 
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * Set auditorName
     *
     * @param string $auditorName
     * @return WelfareOrder
     */
    public function setAuditorName($auditorName)
    {
        $this->auditorName = $auditorName;

        return $this;
    }

    /**
     * Get auditorName
     *
     * @return string 
     */
    public function getAuditorName()
    {
        return $this->auditorName;
    }
    /**
     * @var string
     */
    private $everyNum;


    /**
     * Set everyNum
     *
     * @param string $everyNum
     * @return WelfareOrder
     */
    public function setEveryNum($everyNum)
    {
        $this->everyNum = $everyNum;

        return $this;
    }

    /**
     * Get everyNum
     *
     * @return string 
     */
    public function getEveryNum()
    {
        return $this->everyNum;
    }
}
