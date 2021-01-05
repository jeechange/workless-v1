<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyService
 */
class CompanyService
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
    private $serviceId;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $sCode;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var integer
     */
    private $totals;

    /**
     * @var integer
     */
    private $useTotals;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $expireTime;

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
     * @return CompanyService
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
     * Set serviceId
     *
     * @param integer $serviceId
     * @return CompanyService
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get serviceId
     *
     * @return integer 
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return CompanyService
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
     * Set sCode
     *
     * @param string $sCode
     * @return CompanyService
     */
    public function setSCode($sCode)
    {
        $this->sCode = $sCode;

        return $this;
    }

    /**
     * Get sCode
     *
     * @return string 
     */
    public function getSCode()
    {
        return $this->sCode;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return CompanyService
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
     * Set totals
     *
     * @param integer $totals
     * @return CompanyService
     */
    public function setTotals($totals)
    {
        $this->totals = $totals;

        return $this;
    }

    /**
     * Get totals
     *
     * @return integer 
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * Set useTotals
     *
     * @param integer $useTotals
     * @return CompanyService
     */
    public function setUseTotals($useTotals)
    {
        $this->useTotals = $useTotals;

        return $this;
    }

    /**
     * Get useTotals
     *
     * @return integer 
     */
    public function getUseTotals()
    {
        return $this->useTotals;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return CompanyService
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
     * Set expireTime
     *
     * @param \DateTime $expireTime
     * @return CompanyService
     */
    public function setExpireTime($expireTime)
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    /**
     * Get expireTime
     *
     * @return \DateTime 
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return CompanyService
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
