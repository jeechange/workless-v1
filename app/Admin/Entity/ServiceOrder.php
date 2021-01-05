<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceOrder
 */
class ServiceOrder
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
    private $serviceId;

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $payTime;

    /**
     * @var integer
     */
    private $payTypes;

    /**
     * @var integer
     */
    private $nums;

    /**
     * @var string
     */
    private $money;

    /**
     * @var \DateTime
     */
    private $doneTime;

    /**
     * @var integer
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
     * Set sid
     *
     * @param integer $sid
     * @return ServiceOrder
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
     * @return ServiceOrder
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
     * Set serviceId
     *
     * @param integer $serviceId
     * @return ServiceOrder
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get serviceId
     *
     * @return integer 
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     * @return ServiceOrder
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return ServiceOrder
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
     * Set payTime
     *
     * @param \DateTime $payTime
     * @return ServiceOrder
     */
    public function setPayTime($payTime)
    {
        $this->payTime = $payTime;

        return $this;
    }

    /**
     * Get payTime
     *
     * @return \DateTime 
     */
    public function getPayTime()
    {
        return $this->payTime;
    }

    /**
     * Set payTypes
     *
     * @param integer $payTypes
     * @return ServiceOrder
     */
    public function setPayTypes($payTypes)
    {
        $this->payTypes = $payTypes;

        return $this;
    }

    /**
     * Get payTypes
     *
     * @return integer 
     */
    public function getPayTypes()
    {
        return $this->payTypes;
    }

    /**
     * Set nums
     *
     * @param integer $nums
     * @return ServiceOrder
     */
    public function setNums($nums)
    {
        $this->nums = $nums;

        return $this;
    }

    /**
     * Get nums
     *
     * @return integer 
     */
    public function getNums()
    {
        return $this->nums;
    }

    /**
     * Set money
     *
     * @param string $money
     * @return ServiceOrder
     */
    public function setMoney($money)
    {
        $this->money = $money;

        return $this;
    }

    /**
     * Get money
     *
     * @return string 
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Set doneTime
     *
     * @param \DateTime $doneTime
     * @return ServiceOrder
     */
    public function setDoneTime($doneTime)
    {
        $this->doneTime = $doneTime;

        return $this;
    }

    /**
     * Get doneTime
     *
     * @return \DateTime 
     */
    public function getDoneTime()
    {
        return $this->doneTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return ServiceOrder
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
     * @var integer
     */
    private $types;


    /**
     * Set types
     *
     * @param integer $types
     * @return ServiceOrder
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
