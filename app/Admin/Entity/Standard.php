<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Standard
 */
class Standard
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
     * @var integer
     */
    private $classify;

    /**
     * @var integer
     */
    private $subClassify;

    /**
     * @var integer
     */
    private $acorn;

    /**
     * @var integer
     */
    private $methods;

    /**
     * @var integer
     */
    private $cycle;

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
     * @return Standard
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
     * Set classify
     *
     * @param integer $classify
     * @return Standard
     */
    public function setClassify($classify)
    {
        $this->classify = $classify;

        return $this;
    }

    /**
     * Get classify
     *
     * @return integer 
     */
    public function getClassify()
    {
        return $this->classify;
    }

    /**
     * Set subClassify
     *
     * @param integer $subClassify
     * @return Standard
     */
    public function setSubClassify($subClassify)
    {
        $this->subClassify = $subClassify;

        return $this;
    }

    /**
     * Get subClassify
     *
     * @return integer 
     */
    public function getSubClassify()
    {
        return $this->subClassify;
    }

    /**
     * Set acorn
     *
     * @param integer $acorn
     * @return Standard
     */
    public function setAcorn($acorn)
    {
        $this->acorn = $acorn;

        return $this;
    }

    /**
     * Get acorn
     *
     * @return integer 
     */
    public function getAcorn()
    {
        return $this->acorn;
    }

    /**
     * Set methods
     *
     * @param integer $methods
     * @return Standard
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * Get methods
     *
     * @return integer 
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Set cycle
     *
     * @param integer $cycle
     * @return Standard
     */
    public function setCycle($cycle)
    {
        $this->cycle = $cycle;

        return $this;
    }

    /**
     * Get cycle
     *
     * @return integer 
     */
    public function getCycle()
    {
        return $this->cycle;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Standard
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
     * @var string
     */
    private $memo;


    /**
     * Set memo
     *
     * @param string $memo
     * @return Standard
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
     * @var \DateTime
     */
    private $addTime;


    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Standard
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
    private $sid;

    /**
     * @var string
     */
    private $sNo;


    /**
     * Set sid
     *
     * @param integer $sid
     * @return Standard
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
     * Set sNo
     *
     * @param string $sNo
     * @return Standard
     */
    public function setSNo($sNo)
    {
        $this->sNo = $sNo;

        return $this;
    }

    /**
     * Get sNo
     *
     * @return string 
     */
    public function getSNo()
    {
        return $this->sNo;
    }
    /**
     * @var integer
     */
    private $hot;


    /**
     * Set hot
     *
     * @param integer $hot
     * @return Standard
     */
    public function setHot($hot)
    {
        $this->hot = $hot;

        return $this;
    }

    /**
     * Get hot
     *
     * @return integer 
     */
    public function getHot()
    {
        return $this->hot;
    }
    /**
     * @var integer
     */
    private $workload;


    /**
     * Set workload
     *
     * @param integer $workload
     * @return Standard
     */
    public function setWorkload($workload)
    {
        $this->workload = $workload;

        return $this;
    }

    /**
     * Get workload
     *
     * @return integer 
     */
    public function getWorkload()
    {
        return $this->workload;
    }
    /**
     * @var integer
     */
    private $types;


    /**
     * Set types
     *
     * @param integer $types
     * @return Standard
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
}
