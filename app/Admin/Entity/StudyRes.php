<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StudyRes
 */
class StudyRes
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
    private $tid;

    /**
     * @var integer
     */
    private $stuId;

    /**
     * @var integer
     */
    private $settingId;

    /**
     * @var \DateTime
     */
    private $addTime;

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
     * @return StudyRes
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
     * Set tid
     *
     * @param integer $tid
     * @return StudyRes
     */
    public function setTid($tid)
    {
        $this->tid = $tid;

        return $this;
    }

    /**
     * Get tid
     *
     * @return integer 
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * Set stuId
     *
     * @param integer $stuId
     * @return StudyRes
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
     * Set settingId
     *
     * @param integer $settingId
     * @return StudyRes
     */
    public function setSettingId($settingId)
    {
        $this->settingId = $settingId;

        return $this;
    }

    /**
     * Get settingId
     *
     * @return integer 
     */
    public function getSettingId()
    {
        return $this->settingId;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return StudyRes
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
     * Set status
     *
     * @param integer $status
     * @return StudyRes
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
    private $aid;


    /**
     * Set aid
     *
     * @param integer $aid
     * @return StudyRes
     */
    public function setAid($aid)
    {
        $this->aid = $aid;

        return $this;
    }

    /**
     * Get aid
     *
     * @return integer 
     */
    public function getAid()
    {
        return $this->aid;
    }
}
