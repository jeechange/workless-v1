<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StaffGroup
 */
class StaffGroup
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var integer
     */
    private $leader;

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
     * Set names
     *
     * @param string $names
     * @return StaffGroup
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
     * Set subject
     *
     * @param string $subject
     * @return StaffGroup
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set leader
     *
     * @param integer $leader
     * @return StaffGroup
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
     * Set members
     *
     * @param string $members
     * @return StaffGroup
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
     * @return StaffGroup
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
     * @return StaffGroup
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
    private $sid;

    /**
     * @var integer
     */
    private $helper;


    /**
     * Set sid
     *
     * @param integer $sid
     * @return StaffGroup
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
     * Set helper
     *
     * @param integer $helper
     * @return StaffGroup
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
}
