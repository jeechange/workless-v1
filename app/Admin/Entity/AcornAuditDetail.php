<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AcornAuditDetail
 */
class AcornAuditDetail
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
    private $auditId;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $fromUser;

    /**
     * @var string
     */
    private $executor;

    /**
     * @var string
     */
    private $sNames;

    /**
     * @var string
     */
    private $scNames;

    /**
     * @var string
     */
    private $acorn;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $auditTime;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $types;


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
     * @return AcornAuditDetail
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
     * Set auditId
     *
     * @param integer $auditId
     * @return AcornAuditDetail
     */
    public function setAuditId($auditId)
    {
        $this->auditId = $auditId;

        return $this;
    }

    /**
     * Get auditId
     *
     * @return integer 
     */
    public function getAuditId()
    {
        return $this->auditId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return AcornAuditDetail
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
     * @return AcornAuditDetail
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
     * Set executor
     *
     * @param string $executor
     * @return AcornAuditDetail
     */
    public function setExecutor($executor)
    {
        $this->executor = $executor;

        return $this;
    }

    /**
     * Get executor
     *
     * @return string 
     */
    public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * Set sNames
     *
     * @param string $sNames
     * @return AcornAuditDetail
     */
    public function setSNames($sNames)
    {
        $this->sNames = $sNames;

        return $this;
    }

    /**
     * Get sNames
     *
     * @return string 
     */
    public function getSNames()
    {
        return $this->sNames;
    }

    /**
     * Set scNames
     *
     * @param string $scNames
     * @return AcornAuditDetail
     */
    public function setScNames($scNames)
    {
        $this->scNames = $scNames;

        return $this;
    }

    /**
     * Get scNames
     *
     * @return string 
     */
    public function getScNames()
    {
        return $this->scNames;
    }

    /**
     * Set acorn
     *
     * @param string $acorn
     * @return AcornAuditDetail
     */
    public function setAcorn($acorn)
    {
        $this->acorn = $acorn;

        return $this;
    }

    /**
     * Get acorn
     *
     * @return string 
     */
    public function getAcorn()
    {
        return $this->acorn;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return AcornAuditDetail
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
     * Set auditTime
     *
     * @param \DateTime $auditTime
     * @return AcornAuditDetail
     */
    public function setAuditTime($auditTime)
    {
        $this->auditTime = $auditTime;

        return $this;
    }

    /**
     * Get auditTime
     *
     * @return \DateTime 
     */
    public function getAuditTime()
    {
        return $this->auditTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return AcornAuditDetail
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
     * @return AcornAuditDetail
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
}
