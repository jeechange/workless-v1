<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPay
 */
class ProductPay
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
    private $productId;

    /**
     * @var string
     */
    private $payTypes;

    /**
     * @var integer
     */
    private $consumer;

    /**
     * @var \DateTime
     */
    private $addTime;

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
     * Set sid
     *
     * @param integer $sid
     * @return ProductPay
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
     * Set productId
     *
     * @param integer $productId
     * @return ProductPay
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
     * Set payTypes
     *
     * @param string $payTypes
     * @return ProductPay
     */
    public function setPayTypes($payTypes)
    {
        $this->payTypes = $payTypes;

        return $this;
    }

    /**
     * Get payTypes
     *
     * @return string 
     */
    public function getPayTypes()
    {
        return $this->payTypes;
    }

    /**
     * Set consumer
     *
     * @param integer $consumer
     * @return ProductPay
     */
    public function setConsumer($consumer)
    {
        $this->consumer = $consumer;

        return $this;
    }

    /**
     * Get consumer
     *
     * @return integer 
     */
    public function getConsumer()
    {
        return $this->consumer;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return ProductPay
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
     * Set status
     *
     * @param string $status
     * @return ProductPay
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
}
