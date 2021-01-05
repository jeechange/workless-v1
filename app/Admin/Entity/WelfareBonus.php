<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WelfareBonus
 */
class WelfareBonus
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
    private $bonus;

    /**
     * @var string
     */
    private $memo;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var integer
     */
    private $status;

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
    private $balance;

    /**
     * @var string
     */
    private $tradable;

    /**
     * @var integer
     */
    private $freeze;


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
     * @return WelfareBonus
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
     * @return WelfareBonus
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
     * Set bonus
     *
     * @param string $bonus
     * @return WelfareBonus
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * Get bonus
     *
     * @return string 
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return WelfareBonus
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return WelfareBonus
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
     * @param integer $status
     * @return WelfareBonus
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
     * Set types
     *
     * @param integer $types
     * @return WelfareBonus
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
     * @return WelfareBonus
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
     * @return WelfareBonus
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
     * Set balance
     *
     * @param string $balance
     * @return WelfareBonus
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
     * Set tradable
     *
     * @param string $tradable
     * @return WelfareBonus
     */
    public function setTradable($tradable)
    {
        $this->tradable = $tradable;

        return $this;
    }

    /**
     * Get tradable
     *
     * @return string 
     */
    public function getTradable()
    {
        return $this->tradable;
    }

    /**
     * Set freeze
     *
     * @param integer $freeze
     * @return WelfareBonus
     */
    public function setFreeze($freeze)
    {
        $this->freeze = $freeze;

        return $this;
    }

    /**
     * Get freeze
     *
     * @return integer 
     */
    public function getFreeze()
    {
        return $this->freeze;
    }
}
