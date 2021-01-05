<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SurveyResult
 */
class SurveyResult
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
    private $surveyId;

    /**
     * @var integer
     */
    private $teamId;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $total;

    /**
     * @var string
     */
    private $userScore;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $scoreTime;

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
     * @return SurveyResult
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
     * Set surveyId
     *
     * @param integer $surveyId
     * @return SurveyResult
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;

        return $this;
    }

    /**
     * Get surveyId
     *
     * @return integer 
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * Set teamId
     *
     * @param integer $teamId
     * @return SurveyResult
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;

        return $this;
    }

    /**
     * Get teamId
     *
     * @return integer 
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return SurveyResult
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
     * Set total
     *
     * @param string $total
     * @return SurveyResult
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string 
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set userScore
     *
     * @param string $userScore
     * @return SurveyResult
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return SurveyResult
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
     * Set scoreTime
     *
     * @param \DateTime $scoreTime
     * @return SurveyResult
     */
    public function setScoreTime($scoreTime)
    {
        $this->scoreTime = $scoreTime;

        return $this;
    }

    /**
     * Get scoreTime
     *
     * @return \DateTime 
     */
    public function getScoreTime()
    {
        return $this->scoreTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return SurveyResult
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
