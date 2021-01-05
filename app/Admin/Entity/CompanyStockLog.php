<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyStockLog
 */
class CompanyStockLog
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
    private $types;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var string
     */
    private $oldVal;

    /**
     * @var string
     */
    private $newVal;

    /**
     * @var string
     */
    private $memo;


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
     * @return CompanyStockLog
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
     * Set types
     *
     * @param integer $types
     * @return CompanyStockLog
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
     * @return CompanyStockLog
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
     * Set oldVal
     *
     * @param string $oldVal
     * @return CompanyStockLog
     */
    public function setOldVal($oldVal)
    {
        $this->oldVal = $oldVal;

        return $this;
    }

    /**
     * Get oldVal
     *
     * @return string 
     */
    public function getOldVal()
    {
        return $this->oldVal;
    }

    /**
     * Set newVal
     *
     * @param string $newVal
     * @return CompanyStockLog
     */
    public function setNewVal($newVal)
    {
        $this->newVal = $newVal;

        return $this;
    }

    /**
     * Get newVal
     *
     * @return string 
     */
    public function getNewVal()
    {
        return $this->newVal;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return CompanyStockLog
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
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return CompanyStockLog
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
