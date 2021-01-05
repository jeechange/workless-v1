<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalRelations
 */
class ExternalRelations
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
     * @var string
     */
    private $tgId;

    /**
     * @var string
     */
    private $phone;

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
     * @return ExternalRelations
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
     * Set tgId
     *
     * @param string $tgId
     * @return ExternalRelations
     */
    public function setTgId($tgId)
    {
        $this->tgId = $tgId;

        return $this;
    }

    /**
     * Get tgId
     *
     * @return string 
     */
    public function getTgId()
    {
        return $this->tgId;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return ExternalRelations
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return ExternalRelations
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
     * @return ExternalRelations
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
    /**
     * @var integer
     */
    private $userId;


    /**
     * Set userId
     *
     * @param integer $userId
     * @return ExternalRelations
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
     * @var string
     */
    private $memo;


    /**
     * Set memo
     *
     * @param string $memo
     * @return ExternalRelations
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
