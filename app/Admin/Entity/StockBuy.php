<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockBuy
 */
class StockBuy
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $collection;

    /**
     * @var string
     */
    private $account;

    /**
     * @var string
     */
    private $accountNo;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $money;

    /**
     * @var string
     */
    private $balance;

    /**
     * @var \DateTime
     */
    private $checkTime;

    /**
     * @var integer
     */
    private $adminId;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $sid;

    /**
     * @var integer
     */
    private $usePriority;

    /**
     * @var string
     */
    private $proof;


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
     * Set userId
     *
     * @param integer $userId
     * @return StockBuy
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
     * Set collection
     *
     * @param string $collection
     * @return StockBuy
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return string 
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set account
     *
     * @param string $account
     * @return StockBuy
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return string 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set accountNo
     *
     * @param string $accountNo
     * @return StockBuy
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;

        return $this;
    }

    /**
     * Get accountNo
     *
     * @return string 
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return StockBuy
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set money
     *
     * @param string $money
     * @return StockBuy
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
     * Set balance
     *
     * @param string $balance
     * @return StockBuy
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string 
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set checkTime
     *
     * @param \DateTime $checkTime
     * @return StockBuy
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
     * Set adminId
     *
     * @param integer $adminId
     * @return StockBuy
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;

        return $this;
    }

    /**
     * Get adminId
     *
     * @return integer 
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return StockBuy
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
     * @return StockBuy
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
     * Set usePriority
     *
     * @param integer $usePriority
     * @return StockBuy
     */
    public function setUsePriority($usePriority)
    {
        $this->usePriority = $usePriority;

        return $this;
    }

    /**
     * Get usePriority
     *
     * @return integer 
     */
    public function getUsePriority()
    {
        return $this->usePriority;
    }

    /**
     * Set proof
     *
     * @param string $proof
     * @return StockBuy
     */
    public function setProof($proof)
    {
        $this->proof = $proof;

        return $this;
    }

    /**
     * Get proof
     *
     * @return string 
     */
    public function getProof()
    {
        return $this->proof;
    }
}
