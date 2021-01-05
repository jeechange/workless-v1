<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskStatistics
 */
class TaskStatistics
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
     * @var integer
     */
    private $issueCount;

    /**
     * @var integer
     */
    private $acceptCount;

    /**
     * @var integer
     */
    private $allotCount;

    /**
     * @var integer
     */
    private $execute;

    /**
     * @var integer
     */
    private $accept;

    /**
     * @var string
     */
    private $realWl;

    /**
     * @var string
     */
    private $totalWl;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var string
     */
    private $day;

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
    private $memo;

    /**
     * @var string
     */
    private $types;

    /**
     * @var integer
     */
    private $taskId;

    /**
     * @var string
     */
    private $coefficient;

    /**
     * @var string
     */
    private $quality;


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
     * @return TaskStatistics
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
     * @return TaskStatistics
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
     * Set issueCount
     *
     * @param integer $issueCount
     * @return TaskStatistics
     */
    public function setIssueCount($issueCount)
    {
        $this->issueCount = $issueCount;

        return $this;
    }

    /**
     * Get issueCount
     *
     * @return integer 
     */
    public function getIssueCount()
    {
        return $this->issueCount;
    }

    /**
     * Set acceptCount
     *
     * @param integer $acceptCount
     * @return TaskStatistics
     */
    public function setAcceptCount($acceptCount)
    {
        $this->acceptCount = $acceptCount;

        return $this;
    }

    /**
     * Get acceptCount
     *
     * @return integer 
     */
    public function getAcceptCount()
    {
        return $this->acceptCount;
    }

    /**
     * Set allotCount
     *
     * @param integer $allotCount
     * @return TaskStatistics
     */
    public function setAllotCount($allotCount)
    {
        $this->allotCount = $allotCount;

        return $this;
    }

    /**
     * Get allotCount
     *
     * @return integer 
     */
    public function getAllotCount()
    {
        return $this->allotCount;
    }

    /**
     * Set execute
     *
     * @param integer $execute
     * @return TaskStatistics
     */
    public function setExecute($execute)
    {
        $this->execute = $execute;

        return $this;
    }

    /**
     * Get execute
     *
     * @return integer 
     */
    public function getExecute()
    {
        return $this->execute;
    }

    /**
     * Set accept
     *
     * @param integer $accept
     * @return TaskStatistics
     */
    public function setAccept($accept)
    {
        $this->accept = $accept;

        return $this;
    }

    /**
     * Get accept
     *
     * @return integer 
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * Set realWl
     *
     * @param string $realWl
     * @return TaskStatistics
     */
    public function setRealWl($realWl)
    {
        $this->realWl = $realWl;

        return $this;
    }

    /**
     * Get realWl
     *
     * @return string 
     */
    public function getRealWl()
    {
        return $this->realWl;
    }

    /**
     * Set totalWl
     *
     * @param string $totalWl
     * @return TaskStatistics
     */
    public function setTotalWl($totalWl)
    {
        $this->totalWl = $totalWl;

        return $this;
    }

    /**
     * Get totalWl
     *
     * @return string 
     */
    public function getTotalWl()
    {
        return $this->totalWl;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TaskStatistics
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
     * Set day
     *
     * @param string $day
     * @return TaskStatistics
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return string 
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set week
     *
     * @param integer $week
     * @return TaskStatistics
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
     * @return TaskStatistics
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
     * @return TaskStatistics
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
     * Set memo
     *
     * @param string $memo
     * @return TaskStatistics
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
     * Set types
     *
     * @param string $types
     * @return TaskStatistics
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
     * Set taskId
     *
     * @param integer $taskId
     * @return TaskStatistics
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;

        return $this;
    }

    /**
     * Get taskId
     *
     * @return integer 
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * Set coefficient
     *
     * @param string $coefficient
     * @return TaskStatistics
     */
    public function setCoefficient($coefficient)
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * Get coefficient
     *
     * @return string 
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }

    /**
     * Set quality
     *
     * @param string $quality
     * @return TaskStatistics
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return string 
     */
    public function getQuality()
    {
        return $this->quality;
    }
    /**
     * @var string
     */
    private $acceptDay;


    /**
     * Set acceptDay
     *
     * @param string $acceptDay
     * @return TaskStatistics
     */
    public function setAcceptDay($acceptDay)
    {
        $this->acceptDay = $acceptDay;

        return $this;
    }

    /**
     * Get acceptDay
     *
     * @return string 
     */
    public function getAcceptDay()
    {
        return $this->acceptDay;
    }
}
