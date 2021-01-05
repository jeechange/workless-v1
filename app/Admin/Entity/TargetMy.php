<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetMy
 */
class TargetMy
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $tId;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $types;


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
     * Set userId
     *
     * @param integer $userId
     * @return TargetMy
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
     * Set tId
     *
     * @param integer $tId
     * @return TargetMy
     */
    public function setTId($tId)
    {
        $this->tId = $tId;

        return $this;
    }

    /**
     * Get tId
     *
     * @return integer 
     */
    public function getTId()
    {
        return $this->tId;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return TargetMy
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
     * Set types
     *
     * @param integer $types
     * @return TargetMy
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
     * @var string
     */
    private $userName;

    /**
     * @var integer
     */
    private $sid;


    /**
     * Set userName
     *
     * @param string $userName
     * @return TargetMy
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string 
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set sid
     *
     * @param integer $sid
     * @return TargetMy
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
     * @var string
     */
    private $memo;


    /**
     * Set memo
     *
     * @param string $memo
     * @return TargetMy
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
    private $aId;


    /**
     * Set aId
     *
     * @param integer $aId
     * @return TargetMy
     */
    public function setAId($aId)
    {
        $this->aId = $aId;

        return $this;
    }

    /**
     * Get aId
     *
     * @return integer 
     */
    public function getAId()
    {
        return $this->aId;
    }
}
