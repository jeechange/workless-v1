<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockLock
 */
class StockLock
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
    private $priceOri;

    /**
     * @var string
     */
    private $stockOri;

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
     * @return StockLock
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
     * @return StockLock
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
     * Set price
     *
     * @param string $price
     * @return StockLock
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
     * @return StockLock
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
     * Set priceOri
     *
     * @param string $priceOri
     * @return StockLock
     */
    public function setPriceOri($priceOri)
    {
        $this->priceOri = $priceOri;

        return $this;
    }

    /**
     * Get priceOri
     *
     * @return string 
     */
    public function getPriceOri()
    {
        return $this->priceOri;
    }

    /**
     * Set stockOri
     *
     * @param string $stockOri
     * @return StockLock
     */
    public function setStockOri($stockOri)
    {
        $this->stockOri = $stockOri;

        return $this;
    }

    /**
     * Get stockOri
     *
     * @return string 
     */
    public function getStockOri()
    {
        return $this->stockOri;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return StockLock
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
