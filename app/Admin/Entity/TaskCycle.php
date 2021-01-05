<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskCycle
 */
class TaskCycle
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $codeNo;

    /**
     * @var integer
     */
    private $cycleTimes;

    /**
     * @var string
     */
    private $cycleStart;

    /**
     * @var string
     */
    private $cycleEnd;

    /**
     * @var \DateTime
     */
    private $firstTime;

    /**
     * @var \DateTime
     */
    private $lastTime;

    /**
     * @var integer
     */
    private $cycleNext;

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
     * Set codeNo
     *
     * @param integer $codeNo
     * @return TaskCycle
     */
    public function setCodeNo($codeNo)
    {
        $this->codeNo = $codeNo;

        return $this;
    }

    /**
     * Get codeNo
     *
     * @return integer 
     */
    public function getCodeNo()
    {
        return $this->codeNo;
    }

    /**
     * Set cycleTimes
     *
     * @param integer $cycleTimes
     * @return TaskCycle
     */
    public function setCycleTimes($cycleTimes)
    {
        $this->cycleTimes = $cycleTimes;

        return $this;
    }

    /**
     * Get cycleTimes
     *
     * @return integer 
     */
    public function getCycleTimes()
    {
        return $this->cycleTimes;
    }

    /**
     * Set cycleStart
     *
     * @param string $cycleStart
     * @return TaskCycle
     */
    public function setCycleStart($cycleStart)
    {
        $this->cycleStart = $cycleStart;

        return $this;
    }

    /**
     * Get cycleStart
     *
     * @return string 
     */
    public function getCycleStart()
    {
        return $this->cycleStart;
    }

    /**
     * Set cycleEnd
     *
     * @param string $cycleEnd
     * @return TaskCycle
     */
    public function setCycleEnd($cycleEnd)
    {
        $this->cycleEnd = $cycleEnd;

        return $this;
    }

    /**
     * Get cycleEnd
     *
     * @return string 
     */
    public function getCycleEnd()
    {
        return $this->cycleEnd;
    }

    /**
     * Set firstTime
     *
     * @param \DateTime $firstTime
     * @return TaskCycle
     */
    public function setFirstTime($firstTime)
    {
        $this->firstTime = $firstTime;

        return $this;
    }

    /**
     * Get firstTime
     *
     * @return \DateTime 
     */
    public function getFirstTime()
    {
        return $this->firstTime;
    }

    /**
     * Set lastTime
     *
     * @param \DateTime $lastTime
     * @return TaskCycle
     */
    public function setLastTime($lastTime)
    {
        $this->lastTime = $lastTime;

        return $this;
    }

    /**
     * Get lastTime
     *
     * @return \DateTime 
     */
    public function getLastTime()
    {
        return $this->lastTime;
    }

    /**
     * Set cycleNext
     *
     * @param integer $cycleNext
     * @return TaskCycle
     */
    public function setCycleNext($cycleNext)
    {
        $this->cycleNext = $cycleNext;

        return $this;
    }

    /**
     * Get cycleNext
     *
     * @return integer 
     */
    public function getCycleNext()
    {
        return $this->cycleNext;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return TaskCycle
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
    private $cycleTypes;


    /**
     * Set cycleTypes
     *
     * @param integer $cycleTypes
     * @return TaskCycle
     */
    public function setCycleTypes($cycleTypes)
    {
        $this->cycleTypes = $cycleTypes;

        return $this;
    }

    /**
     * Get cycleTypes
     *
     * @return integer 
     */
    public function getCycleTypes()
    {
        return $this->cycleTypes;
    }
    /**
     * @var integer
     */
    private $sid;


    /**
     * Set sid
     *
     * @param integer $sid
     * @return TaskCycle
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
     * @var \DateTime
     */
    private $addTime;


    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TaskCycle
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
}
