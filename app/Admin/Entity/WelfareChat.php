<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WelfareChat
 */
class WelfareChat
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
    private $wrId;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $chat;

    /**
     * @var integer
     */
    private $status;

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
     * @return WelfareChat
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
     * Set wrId
     *
     * @param integer $wrId
     * @return WelfareChat
     */
    public function setWrId($wrId)
    {
        $this->wrId = $wrId;

        return $this;
    }

    /**
     * Get wrId
     *
     * @return integer 
     */
    public function getWrId()
    {
        return $this->wrId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return WelfareChat
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
     * Set chat
     *
     * @param string $chat
     * @return WelfareChat
     */
    public function setChat($chat)
    {
        $this->chat = $chat;

        return $this;
    }

    /**
     * Get chat
     *
     * @return string 
     */
    public function getChat()
    {
        return $this->chat;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return WelfareChat
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return WelfareChat
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
