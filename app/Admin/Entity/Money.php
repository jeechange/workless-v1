<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Money
 */
class Money
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
    private $fromUser;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var integer
     */
    private $btypes;

    /**
     * @var string
     */
    private $biz;

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
    private $addTime;

    /**
     * @var string
     */
    private $memo;

    /**
     * @var string
     */
    private $sysMemo;

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
     * Set userId
     *
     * @param integer $userId
     * @return Money
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
     * Set fromUser
     *
     * @param string $fromUser
     * @return Money
     */
    public function setFromUser($fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return string 
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return Money
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
     * Set btypes
     *
     * @param integer $btypes
     * @return Money
     */
    public function setBtypes($btypes)
    {
        $this->btypes = $btypes;

        return $this;
    }

    /**
     * Get btypes
     *
     * @return integer 
     */
    public function getBtypes()
    {
        return $this->btypes;
    }

    /**
     * Set biz
     *
     * @param string $biz
     * @return Money
     */
    public function setBiz($biz)
    {
        $this->biz = $biz;

        return $this;
    }

    /**
     * Get biz
     *
     * @return string 
     */
    public function getBiz()
    {
        return $this->biz;
    }

    /**
     * Set money
     *
     * @param string $money
     * @return Money
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
     * @return Money
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Money
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
     * Set memo
     *
     * @param string $memo
     * @return Money
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

    /**
     * Set sysMemo
     *
     * @param string $sysMemo
     * @return Money
     */
    public function setSysMemo($sysMemo)
    {
        $this->sysMemo = $sysMemo;

        return $this;
    }

    /**
     * Get sysMemo
     *
     * @return string 
     */
    public function getSysMemo()
    {
        return $this->sysMemo;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Money
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
     * @return Money
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
