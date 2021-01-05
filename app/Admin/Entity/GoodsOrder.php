<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsOrder
 */
class GoodsOrder
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
    private $goodsId;

    /**
     * @var string
     */
    private $orderNo;

    /**
     * @var integer
     */
    private $shareId;

    /**
     * @var string
     */
    private $price;

    /**
     * @var integer
     */
    private $num;

    /**
     * @var string
     */
    private $money;

    /**
     * @var integer
     */
    private $payTypes;

    /**
     * @var string
     */
    private $payMoney;

    /**
     * @var string
     */
    private $platform;

    /**
     * @var string
     */
    private $receiver;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $province;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $county;

    /**
     * @var string
     */
    private $address;

    /**
     * @var integer
     */
    private $checker;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $payTime;

    /**
     * @var \DateTime
     */
    private $checkTime;

    /**
     * @var \DateTime
     */
    private $finishTime;

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
     * @return GoodsOrder
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
     * @return GoodsOrder
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
     * Set goodsId
     *
     * @param integer $goodsId
     * @return GoodsOrder
     */
    public function setGoodsId($goodsId)
    {
        $this->goodsId = $goodsId;

        return $this;
    }

    /**
     * Get goodsId
     *
     * @return integer 
     */
    public function getGoodsId()
    {
        return $this->goodsId;
    }

    /**
     * Set orderNo
     *
     * @param string $orderNo
     * @return GoodsOrder
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
     * Set shareId
     *
     * @param integer $shareId
     * @return GoodsOrder
     */
    public function setShareId($shareId)
    {
        $this->shareId = $shareId;

        return $this;
    }

    /**
     * Get shareId
     *
     * @return integer 
     */
    public function getShareId()
    {
        return $this->shareId;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return GoodsOrder
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
     * Set num
     *
     * @param integer $num
     * @return GoodsOrder
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer 
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set money
     *
     * @param string $money
     * @return GoodsOrder
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
     * Set payTypes
     *
     * @param integer $payTypes
     * @return GoodsOrder
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
     * Set payMoney
     *
     * @param string $payMoney
     * @return GoodsOrder
     */
    public function setPayMoney($payMoney)
    {
        $this->payMoney = $payMoney;

        return $this;
    }

    /**
     * Get payMoney
     *
     * @return string 
     */
    public function getPayMoney()
    {
        return $this->payMoney;
    }

    /**
     * Set platform
     *
     * @param string $platform
     * @return GoodsOrder
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return string 
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set receiver
     *
     * @param string $receiver
     * @return GoodsOrder
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return string 
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return GoodsOrder
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set province
     *
     * @param string $province
     * @return GoodsOrder
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return string 
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return GoodsOrder
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set county
     *
     * @param string $county
     * @return GoodsOrder
     */
    public function setCounty($county)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * Get county
     *
     * @return string 
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return GoodsOrder
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set checker
     *
     * @param integer $checker
     * @return GoodsOrder
     */
    public function setChecker($checker)
    {
        $this->checker = $checker;

        return $this;
    }

    /**
     * Get checker
     *
     * @return integer 
     */
    public function getChecker()
    {
        return $this->checker;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return GoodsOrder
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
     * @return GoodsOrder
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
     * Set checkTime
     *
     * @param \DateTime $checkTime
     * @return GoodsOrder
     */
    public function setCheckTime($checkTime)
    {
        $this->checkTime = $checkTime;

        return $this;
    }

    /**
     * Get checkTime
     *
     * @return \DateTime 
     */
    public function getCheckTime()
    {
        return $this->checkTime;
    }

    /**
     * Set finishTime
     *
     * @param \DateTime $finishTime
     * @return GoodsOrder
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
     * Set status
     *
     * @param integer $status
     * @return GoodsOrder
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
     * @var string
     */
    private $memo;


    /**
     * Set memo
     *
     * @param string $memo
     * @return GoodsOrder
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
}
