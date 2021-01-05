<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockTrade
 */
class StockTrade
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
    private $buyId;

    /**
     * @var integer
     */
    private $sellId;

    /**
     * @var string
     */
    private $price;

    /**
     * @var integer
     */
    private $stock;

    /**
     * @var string
     */
    private $money;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $tradeTime;

    /**
     * @var \DateTime
     */
    private $finishTime;

    /**
     * @var integer
     */
    private $types;

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
     * @return StockTrade
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
     * @return StockTrade
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
     * Set buyId
     *
     * @param integer $buyId
     * @return StockTrade
     */
    public function setBuyId($buyId)
    {
        $this->buyId = $buyId;

        return $this;
    }

    /**
     * Get buyId
     *
     * @return integer 
     */
    public function getBuyId()
    {
        return $this->buyId;
    }

    /**
     * Set sellId
     *
     * @param integer $sellId
     * @return StockTrade
     */
    public function setSellId($sellId)
    {
        $this->sellId = $sellId;

        return $this;
    }

    /**
     * Get sellId
     *
     * @return integer 
     */
    public function getSellId()
    {
        return $this->sellId;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return StockTrade
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return StockTrade
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer 
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set money
     *
     * @param string $money
     * @return StockTrade
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return StockTrade
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
     * Set tradeTime
     *
     * @param \DateTime $tradeTime
     * @return StockTrade
     */
    public function setTradeTime($tradeTime)
    {
        $this->tradeTime = $tradeTime;

        return $this;
    }

    /**
     * Get tradeTime
     *
     * @return \DateTime 
     */
    public function getTradeTime()
    {
        return $this->tradeTime;
    }

    /**
     * Set finishTime
     *
     * @param \DateTime $finishTime
     * @return StockTrade
     */
    public function setFinishTime($finishTime)
    {
        $this->finishTime = $finishTime;

        return $this;
    }

    /**
     * Get finishTime
     *
     * @return \DateTime 
     */
    public function getFinishTime()
    {
        return $this->finishTime;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return StockTrade
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
     * Set status
     *
     * @param integer $status
     * @return StockTrade
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
