<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SurveyAcorn
 */
class SurveyAcorn
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
    private $fromUser;

    /**
     * @var integer
     */
    private $auditor;

    /**
     * @var integer
     */
    private $scId;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $acorn;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var string
     */
    private $memo;

    /**
     * @var string
     */
    private $sysMemo;

    /**
     * @var string
     */
    private $balance;


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
     * @return SurveyAcorn
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
     * @return SurveyAcorn
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
     * @param integer $fromUser
     * @return SurveyAcorn
     */
    public function setFromUser($fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return integer 
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set auditor
     *
     * @param integer $auditor
     * @return SurveyAcorn
     */
    public function setAuditor($auditor)
    {
        $this->auditor = $auditor;

        return $this;
    }

    /**
     * Get auditor
     *
     * @return integer 
     */
    public function getAuditor()
    {
        return $this->auditor;
    }

    /**
     * Set scId
     *
     * @param integer $scId
     * @return SurveyAcorn
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
     * Set names
     *
     * @param string $names
     * @return SurveyAcorn
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
     * Set acorn
     *
     * @param string $acorn
     * @return SurveyAcorn
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
     * @return SurveyAcorn
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
     * Set status
     *
     * @param integer $status
     * @return SurveyAcorn
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
     * @return SurveyAcorn
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
     * Set memo
     *
     * @param string $memo
     * @return SurveyAcorn
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
     * Set sysMemo
     *
     * @param string $sysMemo
     * @return SurveyAcorn
     */
    public function setSysMemo($sysMemo)
    {
        $this->sysMemo = $sysMemo;

        return $this;
    }

    /**
     * Get sysMemo
     *
     * @return string 
     */
    public function getSysMemo()
    {
        return $this->sysMemo;
    }

    /**
     * Set balance
     *
     * @param string $balance
     * @return SurveyAcorn
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string 
     */
    public function getBalance()
    {
        return $this->balance;
    }
}
