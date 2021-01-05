<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Survey
 */
class Survey
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
    private $code;

    /**
     * @var integer
     */
    private $scId;

    /**
     * @var integer
     */
    private $standId;

    /**
     * @var string
     */
    private $surveyObject;

    /**
     * @var integer
     */
    private $surveyTeam;

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
     * @var integer
     */
    private $issue;

    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $totalScore;

    /**
     * @var integer
     */
    private $anonymity;

    /**
     * @var string
     */
    private $userScore;

    /**
     * @var string
     */
    private $tags;


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
     * @return Survey
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
     * Set code
     *
     * @param string $code
     * @return Survey
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set scId
     *
     * @param integer $scId
     * @return Survey
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
     * Set standId
     *
     * @param integer $standId
     * @return Survey
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
     * Set surveyObject
     *
     * @param string $surveyObject
     * @return Survey
     */
    public function setSurveyObject($surveyObject)
    {
        $this->surveyObject = $surveyObject;

        return $this;
    }

    /**
     * Get surveyObject
     *
     * @return string 
     */
    public function getSurveyObject()
    {
        return $this->surveyObject;
    }

    /**
     * Set surveyTeam
     *
     * @param integer $surveyTeam
     * @return Survey
     */
    public function setSurveyTeam($surveyTeam)
    {
        $this->surveyTeam = $surveyTeam;

        return $this;
    }

    /**
     * Get surveyTeam
     *
     * @return integer 
     */
    public function getSurveyTeam()
    {
        return $this->surveyTeam;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Survey
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
     * @return Survey
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
     * @return Survey
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
     * Set issue
     *
     * @param integer $issue
     * @return Survey
     */
    public function setIssue($issue)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return integer 
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Survey
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Survey
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
     * Set totalScore
     *
     * @param integer $totalScore
     * @return Survey
     */
    public function setTotalScore($totalScore)
    {
        $this->totalScore = $totalScore;

        return $this;
    }

    /**
     * Get totalScore
     *
     * @return integer 
     */
    public function getTotalScore()
    {
        return $this->totalScore;
    }

    /**
     * Set anonymity
     *
     * @param integer $anonymity
     * @return Survey
     */
    public function setAnonymity($anonymity)
    {
        $this->anonymity = $anonymity;

        return $this;
    }

    /**
     * Get anonymity
     *
     * @return integer 
     */
    public function getAnonymity()
    {
        return $this->anonymity;
    }

    /**
     * Set userScore
     *
     * @param string $userScore
     * @return Survey
     */
    public function setUserScore($userScore)
    {
        $this->userScore = $userScore;

        return $this;
    }

    /**
     * Get userScore
     *
     * @return string 
     */
    public function getUserScore()
    {
        return $this->userScore;
    }

    /**
     * Set tags
     *
     * @param string $tags
     * @return Survey
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }
}
