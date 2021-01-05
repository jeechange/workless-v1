<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SurveyGroup
 */
class SurveyGroup
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
    private $names;

    /**
     * @var integer
     */
    private $leader;

    /**
     * @var integer
     */
    private $helper;

    /**
     * @var string
     */
    private $members;

    /**
     * @var \DateTime
     */
    private $addTime;

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
     * @return SurveyGroup
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
     * Set names
     *
     * @param string $names
     * @return SurveyGroup
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
     * Set leader
     *
     * @param integer $leader
     * @return SurveyGroup
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
     * Set helper
     *
     * @param integer $helper
     * @return SurveyGroup
     */
    public function setHelper($helper)
    {
        $this->helper = $helper;

        return $this;
    }

    /**
     * Get helper
     *
     * @return integer 
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Set members
     *
     * @param string $members
     * @return SurveyGroup
     */
    public function setMembers($members)
    {
        $this->members = $members;

        return $this;
    }

    /**
     * Get members
     *
     * @return string 
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return SurveyGroup
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
     * @return SurveyGroup
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
