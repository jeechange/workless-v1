<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WelfareVoucher
 */
class WelfareVoucher
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
     * @var string
     */
    private $acorn;

    /**
     * @var string
     */
    private $stdClassify;

    /**
     * @var string
     */
    private $department;

    /**
     * @var string
     */
    private $content;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var integer
     */
    private $status;

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
     * Set sid
     *
     * @param integer $sid
     * @return WelfareVoucher
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
     * @return WelfareVoucher
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
     * @return WelfareVoucher
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
     * Set stdClassify
     *
     * @param string $stdClassify
     * @return WelfareVoucher
     */
    public function setStdClassify($stdClassify)
    {
        $this->stdClassify = $stdClassify;

        return $this;
    }

    /**
     * Get stdClassify
     *
     * @return string 
     */
    public function getStdClassify()
    {
        return $this->stdClassify;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return WelfareVoucher
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return WelfareVoucher
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
     * @return WelfareVoucher
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
     * Set status
     *
     * @param integer $status
     * @return WelfareVoucher
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return WelfareVoucher
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
     * @var string
     */
    private $icon;

    /**
     * @var integer
     */
    private $sort;


    /**
     * Set icon
     *
     * @param string $icon
     * @return WelfareVoucher
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
     * Set sort
     *
     * @param integer $sort
     * @return WelfareVoucher
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }
}
