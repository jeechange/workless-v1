<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Staff
 */
class Staff
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
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $roleName;

    /**
     * @var integer
     */
    private $effect;

    /**
     * @var integer
     */
    private $point;

    /**
     * @var string
     */
    private $pwd;

    /**
     * @var string
     */
    private $safepwd;

    /**
     * @var integer
     */
    private $department;

    /**
     * @var string
     */
    private $station;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $qq;

    /**
     * @var string
     */
    private $wx;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var string
     */
    private $memo;

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
     * @return Staff
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
     * Set fullName
     *
     * @param string $fullName
     * @return Staff
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set userName
     *
     * @param string $userName
     * @return Staff
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
     * Set roleName
     *
     * @param string $roleName
     * @return Staff
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;

        return $this;
    }

    /**
     * Get roleName
     *
     * @return string 
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * Set effect
     *
     * @param integer $effect
     * @return Staff
     */
    public function setEffect($effect)
    {
        $this->effect = $effect;

        return $this;
    }

    /**
     * Get effect
     *
     * @return integer 
     */
    public function getEffect()
    {
        return $this->effect;
    }

    /**
     * Set point
     *
     * @param integer $point
     * @return Staff
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return integer 
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set pwd
     *
     * @param string $pwd
     * @return Staff
     */
    public function setPwd($pwd)
    {
        $this->pwd = $pwd;

        return $this;
    }

    /**
     * Get pwd
     *
     * @return string 
     */
    public function getPwd()
    {
        return $this->pwd;
    }

    /**
     * Set safepwd
     *
     * @param string $safepwd
     * @return Staff
     */
    public function setSafepwd($safepwd)
    {
        $this->safepwd = $safepwd;

        return $this;
    }

    /**
     * Get safepwd
     *
     * @return string 
     */
    public function getSafepwd()
    {
        return $this->safepwd;
    }

    /**
     * Set department
     *
     * @param integer $department
     * @return Staff
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return integer 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set station
     *
     * @param string $station
     * @return Staff
     */
    public function setStation($station)
    {
        $this->station = $station;

        return $this;
    }

    /**
     * Get station
     *
     * @return string 
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Staff
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set qq
     *
     * @param string $qq
     * @return Staff
     */
    public function setQq($qq)
    {
        $this->qq = $qq;

        return $this;
    }

    /**
     * Get qq
     *
     * @return string 
     */
    public function getQq()
    {
        return $this->qq;
    }

    /**
     * Set wx
     *
     * @param string $wx
     * @return Staff
     */
    public function setWx($wx)
    {
        $this->wx = $wx;

        return $this;
    }

    /**
     * Get wx
     *
     * @return string 
     */
    public function getWx()
    {
        return $this->wx;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Staff
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Staff
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
     * Set memo
     *
     * @param string $memo
     * @return Staff
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
     * Set status
     *
     * @param integer $status
     * @return Staff
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
    private $userId;


    /**
     * Set userId
     *
     * @param integer $userId
     * @return Staff
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
    private $photo;


    /**
     * Set photo
     *
     * @param string $photo
     * @return Staff
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }
    /**
     * @var string
     */
    private $bonus;


    /**
     * Set bonus
     *
     * @param string $bonus
     * @return Staff
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * Get bonus
     *
     * @return string 
     */
    public function getBonus()
    {
        return $this->bonus;
    }
    /**
     * @var integer
     */
    private $snackNum;


    /**
     * Set snackNum
     *
     * @param integer $snackNum
     * @return Staff
     */
    public function setSnackNum($snackNum)
    {
        $this->snackNum = $snackNum;

        return $this;
    }

    /**
     * Get snackNum
     *
     * @return integer 
     */
    public function getSnackNum()
    {
        return $this->snackNum;
    }
}
