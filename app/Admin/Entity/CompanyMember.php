<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyMember
 */
class CompanyMember
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
     * @var integer
     */
    private $sid;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $intoTime;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var integer
     */
    private $recId;

    /**
     * @var string
     */
    private $acorn;

    /**
     * @var integer
     */
    private $snackNum;

    /**
     * @var string
     */
    private $wxwork;

    /**
     * @var string
     */
    private $ding;

    /**
     * @var \DateTime
     */
    private $leftTime;

    /**
     * @var string
     */
    private $surveyAcorn;

    /**
     * @var string
     */
    private $targetAcorn;

    /**
     * @var integer
     */
    private $leader;

    /**
     * @var string
     */
    private $leaderName;

    /**
     * @var string
     */
    private $money;

    /**
     * @var string
     */
    private $bonus;

    /**
     * @var string
     */
    private $inviteInfo;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var integer
     */
    private $department;

    /**
     * @var integer
     */
    private $station;


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
     * @return CompanyMember
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
     * Set sid
     *
     * @param integer $sid
     * @return CompanyMember
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return CompanyMember
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
     * Set intoTime
     *
     * @param \DateTime $intoTime
     * @return CompanyMember
     */
    public function setIntoTime($intoTime)
    {
        $this->intoTime = $intoTime;

        return $this;
    }

    /**
     * Get intoTime
     *
     * @return \DateTime 
     */
    public function getIntoTime()
    {
        return $this->intoTime;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return CompanyMember
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
     * Set status
     *
     * @param integer $status
     * @return CompanyMember
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
     * Set phone
     *
     * @param string $phone
     * @return CompanyMember
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
     * Set recId
     *
     * @param integer $recId
     * @return CompanyMember
     */
    public function setRecId($recId)
    {
        $this->recId = $recId;

        return $this;
    }

    /**
     * Get recId
     *
     * @return integer 
     */
    public function getRecId()
    {
        return $this->recId;
    }

    /**
     * Set acorn
     *
     * @param string $acorn
     * @return CompanyMember
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
     * Set snackNum
     *
     * @param integer $snackNum
     * @return CompanyMember
     */
    public function setSnackNum($snackNum)
    {
        $this->snackNum = $snackNum;

        return $this;
    }

    /**
     * Get snackNum
     *
     * @return integer 
     */
    public function getSnackNum()
    {
        return $this->snackNum;
    }

    /**
     * Set wxwork
     *
     * @param string $wxwork
     * @return CompanyMember
     */
    public function setWxwork($wxwork)
    {
        $this->wxwork = $wxwork;

        return $this;
    }

    /**
     * Get wxwork
     *
     * @return string 
     */
    public function getWxwork()
    {
        return $this->wxwork;
    }

    /**
     * Set ding
     *
     * @param string $ding
     * @return CompanyMember
     */
    public function setDing($ding)
    {
        $this->ding = $ding;

        return $this;
    }

    /**
     * Get ding
     *
     * @return string 
     */
    public function getDing()
    {
        return $this->ding;
    }

    /**
     * Set leftTime
     *
     * @param \DateTime $leftTime
     * @return CompanyMember
     */
    public function setLeftTime($leftTime)
    {
        $this->leftTime = $leftTime;

        return $this;
    }

    /**
     * Get leftTime
     *
     * @return \DateTime 
     */
    public function getLeftTime()
    {
        return $this->leftTime;
    }

    /**
     * Set surveyAcorn
     *
     * @param string $surveyAcorn
     * @return CompanyMember
     */
    public function setSurveyAcorn($surveyAcorn)
    {
        $this->surveyAcorn = $surveyAcorn;

        return $this;
    }

    /**
     * Get surveyAcorn
     *
     * @return string 
     */
    public function getSurveyAcorn()
    {
        return $this->surveyAcorn;
    }

    /**
     * Set targetAcorn
     *
     * @param string $targetAcorn
     * @return CompanyMember
     */
    public function setTargetAcorn($targetAcorn)
    {
        $this->targetAcorn = $targetAcorn;

        return $this;
    }

    /**
     * Get targetAcorn
     *
     * @return string 
     */
    public function getTargetAcorn()
    {
        return $this->targetAcorn;
    }

    /**
     * Set leader
     *
     * @param integer $leader
     * @return CompanyMember
     */
    public function setLeader($leader)
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * Get leader
     *
     * @return integer 
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * Set leaderName
     *
     * @param string $leaderName
     * @return CompanyMember
     */
    public function setLeaderName($leaderName)
    {
        $this->leaderName = $leaderName;

        return $this;
    }

    /**
     * Get leaderName
     *
     * @return string 
     */
    public function getLeaderName()
    {
        return $this->leaderName;
    }

    /**
     * Set money
     *
     * @param string $money
     * @return CompanyMember
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
     * Set bonus
     *
     * @param string $bonus
     * @return CompanyMember
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
     * Set inviteInfo
     *
     * @param string $inviteInfo
     * @return CompanyMember
     */
    public function setInviteInfo($inviteInfo)
    {
        $this->inviteInfo = $inviteInfo;

        return $this;
    }

    /**
     * Get inviteInfo
     *
     * @return string 
     */
    public function getInviteInfo()
    {
        return $this->inviteInfo;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     * @return CompanyMember
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set department
     *
     * @param integer $department
     * @return CompanyMember
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return integer 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set station
     *
     * @param integer $station
     * @return CompanyMember
     */
    public function setStation($station)
    {
        $this->station = $station;

        return $this;
    }

    /**
     * Get station
     *
     * @return integer 
     */
    public function getStation()
    {
        return $this->station;
    }
}
