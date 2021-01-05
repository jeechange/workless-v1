<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StudyDetail
 */
class StudyDetail
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
    private $stuId;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $taskCount;

    /**
     * @var integer
     */
    private $totalCount;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $doneTime;

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
     * @return StudyDetail
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
     * Set stuId
     *
     * @param integer $stuId
     * @return StudyDetail
     */
    public function setStuId($stuId)
    {
        $this->stuId = $stuId;

        return $this;
    }

    /**
     * Get stuId
     *
     * @return integer 
     */
    public function getStuId()
    {
        return $this->stuId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return StudyDetail
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
     * Set taskCount
     *
     * @param integer $taskCount
     * @return StudyDetail
     */
    public function setTaskCount($taskCount)
    {
        $this->taskCount = $taskCount;

        return $this;
    }

    /**
     * Get taskCount
     *
     * @return integer 
     */
    public function getTaskCount()
    {
        return $this->taskCount;
    }

    /**
     * Set totalCount
     *
     * @param integer $totalCount
     * @return StudyDetail
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * Get totalCount
     *
     * @return integer 
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return StudyDetail
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
     * Set doneTime
     *
     * @param \DateTime $doneTime
     * @return StudyDetail
     */
    public function setDoneTime($doneTime)
    {
        $this->doneTime = $doneTime;

        return $this;
    }

    /**
     * Get doneTime
     *
     * @return \DateTime 
     */
    public function getDoneTime()
    {
        return $this->doneTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return StudyDetail
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
