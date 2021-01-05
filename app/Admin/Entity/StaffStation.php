<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StaffStation
 */
class StaffStation
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
    private $department;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $memo;

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
     * @return StaffStation
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
     * Set department
     *
     * @param integer $department
     * @return StaffStation
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return integer 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return StaffStation
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
     * Set memo
     *
     * @param string $memo
     * @return StaffStation
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
     * Set status
     *
     * @param integer $status
     * @return StaffStation
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
    private $num;


    /**
     * Set num
     *
     * @param integer $num
     * @return StaffStation
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer 
     */
    public function getNum()
    {
        return $this->num;
    }
    /**
     * @var integer
     */
    private $riseAcorn;


    /**
     * Set riseAcorn
     *
     * @param integer $riseAcorn
     * @return StaffStation
     */
    public function setRiseAcorn($riseAcorn)
    {
        $this->riseAcorn = $riseAcorn;

        return $this;
    }

    /**
     * Get riseAcorn
     *
     * @return integer 
     */
    public function getRiseAcorn()
    {
        return $this->riseAcorn;
    }
    /**
     * @var integer
     */
    private $limit;


    /**
     * Set limit
     *
     * @param integer $limit
     * @return StaffStation
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get limit
     *
     * @return integer 
     */
    public function getLimit()
    {
        return $this->limit;
    }
    /**
     * @var integer
     */
    private $limitAcorn;


    /**
     * Set limitAcorn
     *
     * @param integer $limitAcorn
     * @return StaffStation
     */
    public function setLimitAcorn($limitAcorn)
    {
        $this->limitAcorn = $limitAcorn;

        return $this;
    }

    /**
     * Get limitAcorn
     *
     * @return integer 
     */
    public function getLimitAcorn()
    {
        return $this->limitAcorn;
    }
    /**
     * @var integer
     */
    private $sort;


    /**
     * Set sort
     *
     * @param integer $sort
     * @return StaffStation
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
