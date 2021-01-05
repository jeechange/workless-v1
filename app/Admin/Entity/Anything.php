<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Anything
 */
class Anything
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
    private $content;

    /**
     * @var \DateTime
     */
    private $certainTime;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var string
     */
    private $types;

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
     * Set userId
     *
     * @param integer $userId
     * @return Anything
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
     * Set content
     *
     * @param string $content
     * @return Anything
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set certainTime
     *
     * @param \DateTime $certainTime
     * @return Anything
     */
    public function setCertainTime($certainTime)
    {
        $this->certainTime = $certainTime;

        return $this;
    }

    /**
     * Get certainTime
     *
     * @return \DateTime 
     */
    public function getCertainTime()
    {
        return $this->certainTime;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Anything
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set types
     *
     * @param string $types
     * @return Anything
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return string 
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Anything
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
     * @var \DateTime
     */
    private $completeTime;


    /**
     * Set completeTime
     *
     * @param \DateTime $completeTime
     * @return Anything
     */
    public function setCompleteTime($completeTime)
    {
        $this->completeTime = $completeTime;

        return $this;
    }

    /**
     * Get completeTime
     *
     * @return \DateTime 
     */
    public function getCompleteTime()
    {
        return $this->completeTime;
    }
    /**
     * @var integer
     */
    private $tgId;

    /**
     * @var string
     */
    private $tgNames;


    /**
     * Set tgId
     *
     * @param integer $tgId
     * @return Anything
     */
    public function setTgId($tgId)
    {
        $this->tgId = $tgId;

        return $this;
    }

    /**
     * Get tgId
     *
     * @return integer 
     */
    public function getTgId()
    {
        return $this->tgId;
    }

    /**
     * Set tgNames
     *
     * @param string $tgNames
     * @return Anything
     */
    public function setTgNames($tgNames)
    {
        $this->tgNames = $tgNames;

        return $this;
    }

    /**
     * Get tgNames
     *
     * @return string 
     */
    public function getTgNames()
    {
        return $this->tgNames;
    }
    /**
     * @var integer
     */
    private $sid;


    /**
     * Set sid
     *
     * @param integer $sid
     * @return Anything
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
