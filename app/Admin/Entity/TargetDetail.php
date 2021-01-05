<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetDetail
 */
class TargetDetail
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $mId;

    /**
     * @var integer
     */
    private $scId;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $percent;

    /**
     * @var string
     */
    private $selfEval;

    /**
     * @var string
     */
    private $othersEval;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var integer
     */
    private $userId;


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
     * Set mId
     *
     * @param integer $mId
     * @return TargetDetail
     */
    public function setMId($mId)
    {
        $this->mId = $mId;

        return $this;
    }

    /**
     * Get mId
     *
     * @return integer 
     */
    public function getMId()
    {
        return $this->mId;
    }

    /**
     * Set scId
     *
     * @param integer $scId
     * @return TargetDetail
     */
    public function setScId($scId)
    {
        $this->scId = $scId;

        return $this;
    }

    /**
     * Get scId
     *
     * @return integer 
     */
    public function getScId()
    {
        return $this->scId;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return TargetDetail
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
     * Set percent
     *
     * @param string $percent
     * @return TargetDetail
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return string 
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set selfEval
     *
     * @param string $selfEval
     * @return TargetDetail
     */
    public function setSelfEval($selfEval)
    {
        $this->selfEval = $selfEval;

        return $this;
    }

    /**
     * Get selfEval
     *
     * @return string 
     */
    public function getSelfEval()
    {
        return $this->selfEval;
    }

    /**
     * Set othersEval
     *
     * @param string $othersEval
     * @return TargetDetail
     */
    public function setOthersEval($othersEval)
    {
        $this->othersEval = $othersEval;

        return $this;
    }

    /**
     * Get othersEval
     *
     * @return string 
     */
    public function getOthersEval()
    {
        return $this->othersEval;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TargetDetail
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
     * Set userId
     *
     * @param integer $userId
     * @return TargetDetail
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
     * @var string
     */
    private $process;


    /**
     * Set process
     *
     * @param string $process
     * @return TargetDetail
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
     * @return TargetDetail
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
    /**
     * @var string
     */
    private $selfContent;


    /**
     * Set selfContent
     *
     * @param string $selfContent
     * @return TargetDetail
     */
    public function setSelfContent($selfContent)
    {
        $this->selfContent = $selfContent;

        return $this;
    }

    /**
     * Get selfContent
     *
     * @return string 
     */
    public function getSelfContent()
    {
        return $this->selfContent;
    }
    /**
     * @var integer
     */
    private $standardTypes;

    /**
     * @var string
     */
    private $standardSet;

    /**
     * @var string
     */
    private $standardMemo;


    /**
     * Set standardTypes
     *
     * @param integer $standardTypes
     * @return TargetDetail
     */
    public function setStandardTypes($standardTypes)
    {
        $this->standardTypes = $standardTypes;

        return $this;
    }

    /**
     * Get standardTypes
     *
     * @return integer 
     */
    public function getStandardTypes()
    {
        return $this->standardTypes;
    }

    /**
     * Set standardSet
     *
     * @param string $standardSet
     * @return TargetDetail
     */
    public function setStandardSet($standardSet)
    {
        $this->standardSet = $standardSet;

        return $this;
    }

    /**
     * Get standardSet
     *
     * @return string 
     */
    public function getStandardSet()
    {
        return $this->standardSet;
    }

    /**
     * Set standardMemo
     *
     * @param string $standardMemo
     * @return TargetDetail
     */
    public function setStandardMemo($standardMemo)
    {
        $this->standardMemo = $standardMemo;

        return $this;
    }

    /**
     * Get standardMemo
     *
     * @return string 
     */
    public function getStandardMemo()
    {
        return $this->standardMemo;
    }
}
