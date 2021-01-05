<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Study
 */
class Study
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
    private $issueId;

    /**
     * @var string
     */
    private $names;

    /**
     * @var integer
     */
    private $rec;

    /**
     * @var integer
     */
    private $showcase;

    /**
     * @var string
     */
    private $apply;

    /**
     * @var integer
     */
    private $auditTask;

    /**
     * @var integer
     */
    private $auditUser;

    /**
     * @var string
     */
    private $acorn;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var integer
     */
    private $standardId;


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
     * @return Study
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
     * Set issueId
     *
     * @param integer $issueId
     * @return Study
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
     * Set names
     *
     * @param string $names
     * @return Study
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
     * Set rec
     *
     * @param integer $rec
     * @return Study
     */
    public function setRec($rec)
    {
        $this->rec = $rec;

        return $this;
    }

    /**
     * Get rec
     *
     * @return integer 
     */
    public function getRec()
    {
        return $this->rec;
    }

    /**
     * Set showcase
     *
     * @param integer $showcase
     * @return Study
     */
    public function setShowcase($showcase)
    {
        $this->showcase = $showcase;

        return $this;
    }

    /**
     * Get showcase
     *
     * @return integer 
     */
    public function getShowcase()
    {
        return $this->showcase;
    }

    /**
     * Set apply
     *
     * @param string $apply
     * @return Study
     */
    public function setApply($apply)
    {
        $this->apply = $apply;

        return $this;
    }

    /**
     * Get apply
     *
     * @return string 
     */
    public function getApply()
    {
        return $this->apply;
    }

    /**
     * Set auditTask
     *
     * @param integer $auditTask
     * @return Study
     */
    public function setAuditTask($auditTask)
    {
        $this->auditTask = $auditTask;

        return $this;
    }

    /**
     * Get auditTask
     *
     * @return integer 
     */
    public function getAuditTask()
    {
        return $this->auditTask;
    }

    /**
     * Set auditUser
     *
     * @param integer $auditUser
     * @return Study
     */
    public function setAuditUser($auditUser)
    {
        $this->auditUser = $auditUser;

        return $this;
    }

    /**
     * Get auditUser
     *
     * @return integer 
     */
    public function getAuditUser()
    {
        return $this->auditUser;
    }

    /**
     * Set acorn
     *
     * @param string $acorn
     * @return Study
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
     * Set icon
     *
     * @param string $icon
     * @return Study
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Study
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Study
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
     * Set standardId
     *
     * @param integer $standardId
     * @return Study
     */
    public function setStandardId($standardId)
    {
        $this->standardId = $standardId;

        return $this;
    }

    /**
     * Get standardId
     *
     * @return integer 
     */
    public function getStandardId()
    {
        return $this->standardId;
    }
}
