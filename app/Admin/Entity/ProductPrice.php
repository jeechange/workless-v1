<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPrice
 */
class ProductPrice
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
    private $sPrice;

    /**
     * @var string
     */
    private $marketPrice;

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
     * Set productId
     *
     * @param integer $productId
     * @return ProductPrice
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
     * @return ProductPrice
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
     * @return ProductPrice
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
     * @return ProductPrice
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
     * Set sPrice
     *
     * @param string $sPrice
     * @return ProductPrice
     */
    public function setSPrice($sPrice)
    {
        $this->sPrice = $sPrice;

        return $this;
    }

    /**
     * Get sPrice
     *
     * @return string 
     */
    public function getSPrice()
    {
        return $this->sPrice;
    }

    /**
     * Set marketPrice
     *
     * @param string $marketPrice
     * @return ProductPrice
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
     * @return ProductPrice
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
