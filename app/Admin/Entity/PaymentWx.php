<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentWx
 */
class PaymentWx
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $tradeNo;

    /**
     * @var integer
     */
    private $sid;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $openid;

    /**
     * @var string
     */
    private $relateTable;

    /**
     * @var integer
     */
    private $relateId;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var string
     */
    private $money;

    /**
     * @var string
     */
    private $tradeId;

    /**
     * @var string
     */
    private $tradeCode;

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
     * Set tradeNo
     *
     * @param string $tradeNo
     * @return PaymentWx
     */
    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;

        return $this;
    }

    /**
     * Get tradeNo
     *
     * @return string 
     */
    public function getTradeNo()
    {
        return $this->tradeNo;
    }

    /**
     * Set sid
     *
     * @param integer $sid
     * @return PaymentWx
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
     * @return PaymentWx
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
     * Set openid
     *
     * @param string $openid
     * @return PaymentWx
     */
    public function setOpenid($openid)
    {
        $this->openid = $openid;

        return $this;
    }

    /**
     * Get openid
     *
     * @return string 
     */
    public function getOpenid()
    {
        return $this->openid;
    }

    /**
     * Set relateTable
     *
     * @param string $relateTable
     * @return PaymentWx
     */
    public function setRelateTable($relateTable)
    {
        $this->relateTable = $relateTable;

        return $this;
    }

    /**
     * Get relateTable
     *
     * @return string 
     */
    public function getRelateTable()
    {
        return $this->relateTable;
    }

    /**
     * Set relateId
     *
     * @param integer $relateId
     * @return PaymentWx
     */
    public function setRelateId($relateId)
    {
        $this->relateId = $relateId;

        return $this;
    }

    /**
     * Get relateId
     *
     * @return integer 
     */
    public function getRelateId()
    {
        return $this->relateId;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return PaymentWx
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
     * Set money
     *
     * @param string $money
     * @return PaymentWx
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
     * Set tradeId
     *
     * @param string $tradeId
     * @return PaymentWx
     */
    public function setTradeId($tradeId)
    {
        $this->tradeId = $tradeId;

        return $this;
    }

    /**
     * Get tradeId
     *
     * @return string 
     */
    public function getTradeId()
    {
        return $this->tradeId;
    }

    /**
     * Set tradeCode
     *
     * @param string $tradeCode
     * @return PaymentWx
     */
    public function setTradeCode($tradeCode)
    {
        $this->tradeCode = $tradeCode;

        return $this;
    }

    /**
     * Get tradeCode
     *
     * @return string 
     */
    public function getTradeCode()
    {
        return $this->tradeCode;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return PaymentWx
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
}
