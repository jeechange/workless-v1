<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WelfareLucky
 */
class WelfareLucky
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
    private $drawId;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $lucky;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var string
     */
    private $scopes;

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
     * @return WelfareLucky
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
     * Set drawId
     *
     * @param integer $drawId
     * @return WelfareLucky
     */
    public function setDrawId($drawId)
    {
        $this->drawId = $drawId;

        return $this;
    }

    /**
     * Get drawId
     *
     * @return integer 
     */
    public function getDrawId()
    {
        return $this->drawId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return WelfareLucky
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
     * Set title
     *
     * @param string $title
     * @return WelfareLucky
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set lucky
     *
     * @param string $lucky
     * @return WelfareLucky
     */
    public function setLucky($lucky)
    {
        $this->lucky = $lucky;

        return $this;
    }

    /**
     * Get lucky
     *
     * @return string 
     */
    public function getLucky()
    {
        return $this->lucky;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return WelfareLucky
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
     * Set scopes
     *
     * @param string $scopes
     * @return WelfareLucky
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Get scopes
     *
     * @return string 
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return WelfareLucky
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
