<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StandardClassify
 */
class StandardClassify
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
    private $pid;

    /**
     * @var string
     */
    private $names;


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
     * @return StandardClassify
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
     * Set pid
     *
     * @param integer $pid
     * @return StandardClassify
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer 
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return StandardClassify
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
     * @var string
     */
    private $namesEn;


    /**
     * Set namesEn
     *
     * @param string $namesEn
     * @return StandardClassify
     */
    public function setNamesEn($namesEn)
    {
        $this->namesEn = $namesEn;

        return $this;
    }

    /**
     * Get namesEn
     *
     * @return string 
     */
    public function getNamesEn()
    {
        return $this->namesEn;
    }
    /**
     * @var boolean
     */
    private $abbreviation;


    /**
     * Set abbreviation
     *
     * @param boolean $abbreviation
     * @return StandardClassify
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return boolean 
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }
    /**
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return StandardClassify
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
