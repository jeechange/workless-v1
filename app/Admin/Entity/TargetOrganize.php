<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetOrganize
 */
class TargetOrganize
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $depId;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $dNames;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $sid;


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
     * Set depId
     *
     * @param integer $depId
     * @return TargetOrganize
     */
    public function setDepId($depId)
    {
        $this->depId = $depId;

        return $this;
    }

    /**
     * Get depId
     *
     * @return integer 
     */
    public function getDepId()
    {
        return $this->depId;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return TargetOrganize
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
     * Set dNames
     *
     * @param string $dNames
     * @return TargetOrganize
     */
    public function setDNames($dNames)
    {
        $this->dNames = $dNames;

        return $this;
    }

    /**
     * Get dNames
     *
     * @return string 
     */
    public function getDNames()
    {
        return $this->dNames;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TargetOrganize
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
     * Set types
     *
     * @param integer $types
     * @return TargetOrganize
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
     * @return TargetOrganize
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
     * Set sid
     *
     * @param integer $sid
     * @return TargetOrganize
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
}
