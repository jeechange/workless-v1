<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Target
 */
class Target
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
    private $names;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $startTime;

    /**
     * @var \DateTime
     */
    private $endTime;

    /**
     * @var string
     */
    private $remind;

    /**
     * @var string
     */
    private $auditor;

    /**
     * @var string
     */
    private $member;

    /**
     * @var string
     */
    private $othersStaff;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var string
     */
    private $times;

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
     * @return Target
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
     * @return Target
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
     * Set names
     *
     * @param string $names
     * @return Target
     */
    public function setNames($names)
    {
        $this->names = $names;

        return $this;
    }

    /**
     * Get names
     *
     * @return string 
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Target
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
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return Target
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return Target
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set remind
     *
     * @param string $remind
     * @return Target
     */
    public function setRemind($remind)
    {
        $this->remind = $remind;

        return $this;
    }

    /**
     * Get remind
     *
     * @return string 
     */
    public function getRemind()
    {
        return $this->remind;
    }

    /**
     * Set auditor
     *
     * @param string $auditor
     * @return Target
     */
    public function setAuditor($auditor)
    {
        $this->auditor = $auditor;

        return $this;
    }

    /**
     * Get auditor
     *
     * @return string 
     */
    public function getAuditor()
    {
        return $this->auditor;
    }

    /**
     * Set member
     *
     * @param string $member
     * @return Target
     */
    public function setMember($member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return string 
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set othersStaff
     *
     * @param string $othersStaff
     * @return Target
     */
    public function setOthersStaff($othersStaff)
    {
        $this->othersStaff = $othersStaff;

        return $this;
    }

    /**
     * Get othersStaff
     *
     * @return string 
     */
    public function getOthersStaff()
    {
        return $this->othersStaff;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return Target
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
     * Set times
     *
     * @param string $times
     * @return Target
     */
    public function setTimes($times)
    {
        $this->times = $times;

        return $this;
    }

    /**
     * Get times
     *
     * @return string 
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Target
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
    private $mNames;

    /**
     * @var string
     */
    private $oNames;

    /**
     * @var string
     */
    private $aNames;


    /**
     * Set mNames
     *
     * @param string $mNames
     * @return Target
     */
    public function setMNames($mNames)
    {
        $this->mNames = $mNames;

        return $this;
    }

    /**
     * Get mNames
     *
     * @return string 
     */
    public function getMNames()
    {
        return $this->mNames;
    }

    /**
     * Set oNames
     *
     * @param string $oNames
     * @return Target
     */
    public function setONames($oNames)
    {
        $this->oNames = $oNames;

        return $this;
    }

    /**
     * Get oNames
     *
     * @return string 
     */
    public function getONames()
    {
        return $this->oNames;
    }

    /**
     * Set aNames
     *
     * @param string $aNames
     * @return Target
     */
    public function setANames($aNames)
    {
        $this->aNames = $aNames;

        return $this;
    }

    /**
     * Get aNames
     *
     * @return string 
     */
    public function getANames()
    {
        return $this->aNames;
    }
    /**
     * @var integer
     */
    private $aId;


    /**
     * Set aId
     *
     * @param integer $aId
     * @return Target
     */
    public function setAId($aId)
    {
        $this->aId = $aId;

        return $this;
    }

    /**
     * Get aId
     *
     * @return integer 
     */
    public function getAId()
    {
        return $this->aId;
    }
}
