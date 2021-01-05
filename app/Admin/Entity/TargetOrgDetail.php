<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetOrgDetail
 */
class TargetOrgDetail
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $toId;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $content;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var \DateTime
     */
    private $addTime;


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
     * Set toId
     *
     * @param integer $toId
     * @return TargetOrgDetail
     */
    public function setToId($toId)
    {
        $this->toId = $toId;

        return $this;
    }

    /**
     * Get toId
     *
     * @return integer 
     */
    public function getToId()
    {
        return $this->toId;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return TargetOrgDetail
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
     * Set content
     *
     * @param string $content
     * @return TargetOrgDetail
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
     * Set types
     *
     * @param integer $types
     * @return TargetOrgDetail
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TargetOrgDetail
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
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return TargetOrgDetail
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
    private $userId;

    /**
     * @var string
     */
    private $year;

    /**
     * @var string
     */
    private $month;

    /**
     * @var string
     */
    private $quarter;

    /**
     * @var integer
     */
    private $halfYear;


    /**
     * Set userId
     *
     * @param integer $userId
     * @return TargetOrgDetail
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
     * Set year
     *
     * @param string $year
     * @return TargetOrgDetail
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set month
     *
     * @param string $month
     * @return TargetOrgDetail
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return string 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set quarter
     *
     * @param string $quarter
     * @return TargetOrgDetail
     */
    public function setQuarter($quarter)
    {
        $this->quarter = $quarter;

        return $this;
    }

    /**
     * Get quarter
     *
     * @return string 
     */
    public function getQuarter()
    {
        return $this->quarter;
    }

    /**
     * Set halfYear
     *
     * @param integer $halfYear
     * @return TargetOrgDetail
     */
    public function setHalfYear($halfYear)
    {
        $this->halfYear = $halfYear;

        return $this;
    }

    /**
     * Get halfYear
     *
     * @return integer 
     */
    public function getHalfYear()
    {
        return $this->halfYear;
    }
}
