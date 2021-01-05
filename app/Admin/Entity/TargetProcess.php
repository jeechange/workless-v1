<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetProcess
 */
class TargetProcess
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $tdId;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var string
     */
    private $process;


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
     * Set tdId
     *
     * @param integer $tdId
     * @return TargetProcess
     */
    public function setTdId($tdId)
    {
        $this->tdId = $tdId;

        return $this;
    }

    /**
     * Get tdId
     *
     * @return integer 
     */
    public function getTdId()
    {
        return $this->tdId;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TargetProcess
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
     * Set process
     *
     * @param string $process
     * @return TargetProcess
     */
    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return string 
     */
    public function getProcess()
    {
        return $this->process;
    }
    /**
     * @var string
     */
    private $proportion;


    /**
     * Set proportion
     *
     * @param string $proportion
     * @return TargetProcess
     */
    public function setProportion($proportion)
    {
        $this->proportion = $proportion;

        return $this;
    }

    /**
     * Get proportion
     *
     * @return string 
     */
    public function getProportion()
    {
        return $this->proportion;
    }
}
