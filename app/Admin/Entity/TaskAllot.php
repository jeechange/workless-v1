<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskAllot
 */
class TaskAllot
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $tid;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var integer
     */
    private $fromId;

    /**
     * @var string
     */
    private $nextUser;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $endTime;

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
     * Set tid
     *
     * @param integer $tid
     * @return TaskAllot
     */
    public function setTid($tid)
    {
        $this->tid = $tid;

        return $this;
    }

    /**
     * Get tid
     *
     * @return integer 
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return TaskAllot
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
     * Set types
     *
     * @param integer $types
     * @return TaskAllot
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
     * Set fromId
     *
     * @param integer $fromId
     * @return TaskAllot
     */
    public function setFromId($fromId)
    {
        $this->fromId = $fromId;

        return $this;
    }

    /**
     * Get fromId
     *
     * @return integer 
     */
    public function getFromId()
    {
        return $this->fromId;
    }

    /**
     * Set nextUser
     *
     * @param string $nextUser
     * @return TaskAllot
     */
    public function setNextUser($nextUser)
    {
        $this->nextUser = $nextUser;

        return $this;
    }

    /**
     * Get nextUser
     *
     * @return string 
     */
    public function getNextUser()
    {
        return $this->nextUser;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TaskAllot
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
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return TaskAllot
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
     * Set status
     *
     * @param integer $status
     * @return TaskAllot
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
    private $doneTime;


    /**
     * Set doneTime
     *
     * @param \DateTime $doneTime
     * @return TaskAllot
     */
    public function setDoneTime($doneTime)
    {
        $this->doneTime = $doneTime;

        return $this;
    }

    /**
     * Get doneTime
     *
     * @return \DateTime 
     */
    public function getDoneTime()
    {
        return $this->doneTime;
    }
    /**
     * @var integer
     */
    private $acorn;

    /**
     * @var integer
     */
    private $rating;

    /**
     * @var integer
     */
    private $medal;

    /**
     * @var string
     */
    private $learns;


    /**
     * Set acorn
     *
     * @param integer $acorn
     * @return TaskAllot
     */
    public function setAcorn($acorn)
    {
        $this->acorn = $acorn;

        return $this;
    }

    /**
     * Get acorn
     *
     * @return integer 
     */
    public function getAcorn()
    {
        return $this->acorn;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return TaskAllot
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set medal
     *
     * @param integer $medal
     * @return TaskAllot
     */
    public function setMedal($medal)
    {
        $this->medal = $medal;

        return $this;
    }

    /**
     * Get medal
     *
     * @return integer 
     */
    public function getMedal()
    {
        return $this->medal;
    }

    /**
     * Set learns
     *
     * @param string $learns
     * @return TaskAllot
     */
    public function setLearns($learns)
    {
        $this->learns = $learns;

        return $this;
    }

    /**
     * Get learns
     *
     * @return string 
     */
    public function getLearns()
    {
        return $this->learns;
    }
    /**
     * @var integer
     */
    private $accept;


    /**
     * Set accept
     *
     * @param integer $accept
     * @return TaskAllot
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
     * @var integer
     */
    private $acceptDay;

    /**
     * @var integer
     */
    private $acceptHard;

    /**
     * @var integer
     */
    private $acceptQuality;


    /**
     * Set acceptDay
     *
     * @param integer $acceptDay
     * @return TaskAllot
     */
    public function setAcceptDay($acceptDay)
    {
        $this->acceptDay = $acceptDay;

        return $this;
    }

    /**
     * Get acceptDay
     *
     * @return integer 
     */
    public function getAcceptDay()
    {
        return $this->acceptDay;
    }

    /**
     * Set acceptHard
     *
     * @param integer $acceptHard
     * @return TaskAllot
     */
    public function setAcceptHard($acceptHard)
    {
        $this->acceptHard = $acceptHard;

        return $this;
    }

    /**
     * Get acceptHard
     *
     * @return integer 
     */
    public function getAcceptHard()
    {
        return $this->acceptHard;
    }

    /**
     * Set acceptQuality
     *
     * @param integer $acceptQuality
     * @return TaskAllot
     */
    public function setAcceptQuality($acceptQuality)
    {
        $this->acceptQuality = $acceptQuality;

        return $this;
    }

    /**
     * Get acceptQuality
     *
     * @return integer 
     */
    public function getAcceptQuality()
    {
        return $this->acceptQuality;
    }
    /**
     * @var string
     */
    private $workload;


    /**
     * Set workload
     *
     * @param string $workload
     * @return TaskAllot
     */
    public function setWorkload($workload)
    {
        $this->workload = $workload;

        return $this;
    }

    /**
     * Get workload
     *
     * @return string 
     */
    public function getWorkload()
    {
        return $this->workload;
    }
    /**
     * @var integer
     */
    private $recheckId;


    /**
     * Set recheckId
     *
     * @param integer $recheckId
     * @return TaskAllot
     */
    public function setRecheckId($recheckId)
    {
        $this->recheckId = $recheckId;

        return $this;
    }

    /**
     * Get recheckId
     *
     * @return integer 
     */
    public function getRecheckId()
    {
        return $this->recheckId;
    }
    /**
     * @var \DateTime
     */
    private $acceptTime;


    /**
     * Set acceptTime
     *
     * @param \DateTime $acceptTime
     * @return TaskAllot
     */
    public function setAcceptTime($acceptTime)
    {
        $this->acceptTime = $acceptTime;

        return $this;
    }

    /**
     * Get acceptTime
     *
     * @return \DateTime 
     */
    public function getAcceptTime()
    {
        return $this->acceptTime;
    }
}
