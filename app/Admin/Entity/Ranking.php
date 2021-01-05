<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ranking
 */
class Ranking
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
    private $issueId;

    /**
     * @var string
     */
    private $acorn;

    /**
     * @var string
     */
    private $fromUser;

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
    private $week;

    /**
     * @var integer
     */
    private $month;

    /**
     * @var integer
     */
    private $year;

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
     * @return Ranking
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
     * Set issueId
     *
     * @param integer $issueId
     * @return Ranking
     */
    public function setIssueId($issueId)
    {
        $this->issueId = $issueId;

        return $this;
    }

    /**
     * Get issueId
     *
     * @return integer 
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * Set acorn
     *
     * @param string $acorn
     * @return Ranking
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
     * Set fromUser
     *
     * @param string $fromUser
     * @return Ranking
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
     * Set memo
     *
     * @param string $memo
     * @return Ranking
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
     * @return Ranking
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
     * Set week
     *
     * @param integer $week
     * @return Ranking
     */
    public function setWeek($week)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * Get week
     *
     * @return integer 
     */
    public function getWeek()
    {
        return $this->week;
    }

    /**
     * Set month
     *
     * @param integer $month
     * @return Ranking
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Ranking
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set types
     *
     * @param string $types
     * @return Ranking
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
     * @return Ranking
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
     * @var integer
     */
    private $depId;

    /**
     * @var integer
     */
    private $groupId;

    /**
     * @var integer
     */
    private $atypes;


    /**
     * Set depId
     *
     * @param integer $depId
     * @return Ranking
     */
    public function setDepId($depId)
    {
        $this->depId = $depId;

        return $this;
    }

    /**
     * Get depId
     *
     * @return integer 
     */
    public function getDepId()
    {
        return $this->depId;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     * @return Ranking
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set atypes
     *
     * @param integer $atypes
     * @return Ranking
     */
    public function setAtypes($atypes)
    {
        $this->atypes = $atypes;

        return $this;
    }

    /**
     * Get atypes
     *
     * @return integer 
     */
    public function getAtypes()
    {
        return $this->atypes;
    }
    /**
     * @var integer
     */
    private $sid;

    /**
     * @var integer
     */
    private $scId;


    /**
     * Set sid
     *
     * @param integer $sid
     * @return Ranking
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
     * Set scId
     *
     * @param integer $scId
     * @return Ranking
     */
    public function setScId($scId)
    {
        $this->scId = $scId;

        return $this;
    }

    /**
     * Get scId
     *
     * @return integer 
     */
    public function getScId()
    {
        return $this->scId;
    }
    /**
     * @var integer
     */
    private $applicant;


    /**
     * Set applicant
     *
     * @param integer $applicant
     * @return Ranking
     */
    public function setApplicant($applicant)
    {
        $this->applicant = $applicant;

        return $this;
    }

    /**
     * Get applicant
     *
     * @return integer 
     */
    public function getApplicant()
    {
        return $this->applicant;
    }
    /**
     * @var integer
     */
    private $standId;


    /**
     * Set standId
     *
     * @param integer $standId
     * @return Ranking
     */
    public function setStandId($standId)
    {
        $this->standId = $standId;

        return $this;
    }

    /**
     * Get standId
     *
     * @return integer 
     */
    public function getStandId()
    {
        return $this->standId;
    }
    /**
     * @var integer
     */
    private $quarter;


    /**
     * Set quarter
     *
     * @param integer $quarter
     * @return Ranking
     */
    public function setQuarter($quarter)
    {
        $this->quarter = $quarter;

        return $this;
    }

    /**
     * Get quarter
     *
     * @return integer 
     */
    public function getQuarter()
    {
        return $this->quarter;
    }
    /**
     * @var integer
     */
    private $seasons;


    /**
     * Set seasons
     *
     * @param integer $seasons
     * @return Ranking
     */
    public function setSeasons($seasons)
    {
        $this->seasons = $seasons;

        return $this;
    }

    /**
     * Get seasons
     *
     * @return integer 
     */
    public function getSeasons()
    {
        return $this->seasons;
    }
    /**
     * @var integer
     */
    private $pId;


    /**
     * Set pId
     *
     * @param integer $pId
     * @return Ranking
     */
    public function setPId($pId)
    {
        $this->pId = $pId;

        return $this;
    }

    /**
     * Get pId
     *
     * @return integer 
     */
    public function getPId()
    {
        return $this->pId;
    }
}
