<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductSprice
 */
class ProductSprice
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $productId;

    /**
     * @var integer
     */
    private $skuId;

    /**
     * @var integer
     */
    private $levels;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $marketPrice;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $sid;


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
     * Set productId
     *
     * @param integer $productId
     * @return ProductSprice
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set skuId
     *
     * @param integer $skuId
     * @return ProductSprice
     */
    public function setSkuId($skuId)
    {
        $this->skuId = $skuId;

        return $this;
    }

    /**
     * Get skuId
     *
     * @return integer 
     */
    public function getSkuId()
    {
        return $this->skuId;
    }

    /**
     * Set levels
     *
     * @param integer $levels
     * @return ProductSprice
     */
    public function setLevels($levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Get levels
     *
     * @return integer 
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return ProductSprice
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
     * Set marketPrice
     *
     * @param string $marketPrice
     * @return ProductSprice
     */
    public function setMarketPrice($marketPrice)
    {
        $this->marketPrice = $marketPrice;

        return $this;
    }

    /**
     * Get marketPrice
     *
     * @return string 
     */
    public function getMarketPrice()
    {
        return $this->marketPrice;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return ProductSprice
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
     * Set sid
     *
     * @param integer $sid
     * @return ProductSprice
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
