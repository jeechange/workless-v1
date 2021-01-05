<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Supplier
 */
class Supplier
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $roleName;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $pwd;

    /**
     * @var string
     */
    private $pwd2;

    /**
     * @var integer
     */
    private $rid;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $photo;

    /**
     * @var integer
     */
    private $qq;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $birthday;

    /**
     * @var integer
     */
    private $sex;

    /**
     * @var string
     */
    private $area;

    /**
     * @var string
     */
    private $sNames;

    /**
     * @var \DateTime
     */
    private $expireTime;

    /**
     * @var string
     */
    private $memo;


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
     * Set userName
     *
     * @param string $userName
     * @return Supplier
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
     * Set phone
     *
     * @param string $phone
     * @return Supplier
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
     * Set roleName
     *
     * @param string $roleName
     * @return Supplier
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
     * Set fullName
     *
     * @param string $fullName
     * @return Supplier
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
     * Set pwd
     *
     * @param string $pwd
     * @return Supplier
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
     * Set pwd2
     *
     * @param string $pwd2
     * @return Supplier
     */
    public function setPwd2($pwd2)
    {
        $this->pwd2 = $pwd2;

        return $this;
    }

    /**
     * Get pwd2
     *
     * @return string 
     */
    public function getPwd2()
    {
        return $this->pwd2;
    }

    /**
     * Set rid
     *
     * @param integer $rid
     * @return Supplier
     */
    public function setRid($rid)
    {
        $this->rid = $rid;

        return $this;
    }

    /**
     * Get rid
     *
     * @return integer 
     */
    public function getRid()
    {
        return $this->rid;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Supplier
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
     * @return Supplier
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
     * Set photo
     *
     * @param string $photo
     * @return Supplier
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
     * Set qq
     *
     * @param integer $qq
     * @return Supplier
     */
    public function setQq($qq)
    {
        $this->qq = $qq;

        return $this;
    }

    /**
     * Get qq
     *
     * @return integer 
     */
    public function getQq()
    {
        return $this->qq;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Supplier
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
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return Supplier
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set sex
     *
     * @param integer $sex
     * @return Supplier
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return integer 
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set area
     *
     * @param string $area
     * @return Supplier
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string 
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set sNames
     *
     * @param string $sNames
     * @return Supplier
     */
    public function setSNames($sNames)
    {
        $this->sNames = $sNames;

        return $this;
    }

    /**
     * Get sNames
     *
     * @return string 
     */
    public function getSNames()
    {
        return $this->sNames;
    }

    /**
     * Set expireTime
     *
     * @param \DateTime $expireTime
     * @return Supplier
     */
    public function setExpireTime($expireTime)
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    /**
     * Get expireTime
     *
     * @return \DateTime 
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return Supplier
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
    private $industry;


    /**
     * Set industry
     *
     * @param integer $industry
     * @return Supplier
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;

        return $this;
    }

    /**
     * Get industry
     *
     * @return integer 
     */
    public function getIndustry()
    {
        return $this->industry;
    }
    /**
     * @var string
     */
    private $userCode;


    /**
     * Set userCode
     *
     * @param string $userCode
     * @return Supplier
     */
    public function setUserCode($userCode)
    {
        $this->userCode = $userCode;

        return $this;
    }

    /**
     * Get userCode
     *
     * @return string 
     */
    public function getUserCode()
    {
        return $this->userCode;
    }
}
