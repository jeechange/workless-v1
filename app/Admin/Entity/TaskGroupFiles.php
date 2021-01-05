<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskGroupFiles
 */
class TaskGroupFiles
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
    private $userId;

    /**
     * @var integer
     */
    private $gid;

    /**
     * @var integer
     */
    private $pid;

    /**
     * @var string
     */
    private $types;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var integer
     */
    private $sort;


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
     * @return TaskGroupFiles
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
     * Set userId
     *
     * @param integer $userId
     * @return TaskGroupFiles
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
     * Set gid
     *
     * @param integer $gid
     * @return TaskGroupFiles
     */
    public function setGid($gid)
    {
        $this->gid = $gid;

        return $this;
    }

    /**
     * Get gid
     *
     * @return integer 
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * Set pid
     *
     * @param integer $pid
     * @return TaskGroupFiles
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
     * Set types
     *
     * @param string $types
     * @return TaskGroupFiles
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return string 
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return TaskGroupFiles
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
     * Set filePath
     *
     * @param string $filePath
     * @return TaskGroupFiles
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath
     *
     * @return string 
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set suffix
     *
     * @param string $suffix
     * @return TaskGroupFiles
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Get suffix
     *
     * @return string 
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return TaskGroupFiles
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return TaskGroupFiles
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }
    /**
     * @var \DateTime
     */
    private $alterTime;

    /**
     * @var string
     */
    private $memo;


    /**
     * Set alterTime
     *
     * @param \DateTime $alterTime
     * @return TaskGroupFiles
     */
    public function setAlterTime($alterTime)
    {
        $this->alterTime = $alterTime;

        return $this;
    }

    /**
     * Get alterTime
     *
     * @return \DateTime 
     */
    public function getAlterTime()
    {
        return $this->alterTime;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return TaskGroupFiles
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
}
