<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 */
class Company
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $names;

    /**
     * @var integer
     */
    private $superid;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $renewTime;

    /**
     * @var \DateTime
     */
    private $expireTime;

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
     * Set names
     *
     * @param string $names
     * @return Company
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
     * Set superid
     *
     * @param integer $superid
     * @return Company
     */
    public function setSuperid($superid)
    {
        $this->superid = $superid;

        return $this;
    }

    /**
     * Get superid
     *
     * @return integer 
     */
    public function getSuperid()
    {
        return $this->superid;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Company
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
     * Set renewTime
     *
     * @param \DateTime $renewTime
     * @return Company
     */
    public function setRenewTime($renewTime)
    {
        $this->renewTime = $renewTime;

        return $this;
    }

    /**
     * Get renewTime
     *
     * @return \DateTime 
     */
    public function getRenewTime()
    {
        return $this->renewTime;
    }

    /**
     * Set expireTime
     *
     * @param \DateTime $expireTime
     * @return Company
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
     * Set status
     *
     * @param integer $status
     * @return Company
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
     * @var string
     */
    private $shortNames;

    /**
     * @var integer
     */
    private $industry;

    /**
     * @var integer
     */
    private $levels;

    /**
     * @var integer
     */
    private $source;

    /**
     * @var string
     */
    private $legal;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $memo;


    /**
     * Set shortNames
     *
     * @param string $shortNames
     * @return Company
     */
    public function setShortNames($shortNames)
    {
        $this->shortNames = $shortNames;

        return $this;
    }

    /**
     * Get shortNames
     *
     * @return string 
     */
    public function getShortNames()
    {
        return $this->shortNames;
    }

    /**
     * Set industry
     *
     * @param integer $industry
     * @return Company
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
     * Set levels
     *
     * @param integer $levels
     * @return Company
     */
    public function setLevels($levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Get levels
     *
     * @return integer 
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Set source
     *
     * @param integer $source
     * @return Company
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return integer 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set legal
     *
     * @param string $legal
     * @return Company
     */
    public function setLegal($legal)
    {
        $this->legal = $legal;

        return $this;
    }

    /**
     * Get legal
     *
     * @return string 
     */
    public function getLegal()
    {
        return $this->legal;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Company
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return Company
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
    private $scales;


    /**
     * Set scales
     *
     * @param integer $scales
     * @return Company
     */
    public function setScales($scales)
    {
        $this->scales = $scales;

        return $this;
    }

    /**
     * Get scales
     *
     * @return integer 
     */
    public function getScales()
    {
        return $this->scales;
    }
    /**
     * @var string
     */
    private $keywords;


    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Company
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
    /**
     * @var string
     */
    private $codeNo;


    /**
     * Set codeNo
     *
     * @param string $codeNo
     * @return Company
     */
    public function setCodeNo($codeNo)
    {
        $this->codeNo = $codeNo;

        return $this;
    }

    /**
     * Get codeNo
     *
     * @return string 
     */
    public function getCodeNo()
    {
        return $this->codeNo;
    }
    /**
     * @var string
     */
    private $bonus;


    /**
     * Set bonus
     *
     * @param string $bonus
     * @return Company
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
     * @var string
     */
    private $subSuperid;


    /**
     * Set subSuperid
     *
     * @param string $subSuperid
     * @return Company
     */
    public function setSubSuperid($subSuperid)
    {
        $this->subSuperid = $subSuperid;

        return $this;
    }

    /**
     * Get subSuperid
     *
     * @return string 
     */
    public function getSubSuperid()
    {
        return $this->subSuperid;
    }
    /**
     * @var string
     */
    private $province;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $area;


    /**
     * Set province
     *
     * @param string $province
     * @return Company
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return string 
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Company
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set area
     *
     * @param string $area
     * @return Company
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
     * @var string
     */
    private $hfSignature;


    /**
     * Set hfSignature
     *
     * @param string $hfSignature
     * @return Company
     */
    public function setHfSignature($hfSignature)
    {
        $this->hfSignature = $hfSignature;

        return $this;
    }

    /**
     * Get hfSignature
     *
     * @return string 
     */
    public function getHfSignature()
    {
        return $this->hfSignature;
    }
    /**
     * @var string
     */
    private $maxScore;


    /**
     * Set maxScore
     *
     * @param string $maxScore
     * @return Company
     */
    public function setMaxScore($maxScore)
    {
        $this->maxScore = $maxScore;

        return $this;
    }

    /**
     * Get maxScore
     *
     * @return string 
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }
    /**
     * @var string
     */
    private $apps;


    /**
     * Set apps
     *
     * @param string $apps
     * @return Company
     */
    public function setApps($apps)
    {
        $this->apps = $apps;

        return $this;
    }

    /**
     * Get apps
     *
     * @return string 
     */
    public function getApps()
    {
        return $this->apps;
    }
    /**
     * @var integer
     */
    private $userCount;


    /**
     * Set userCount
     *
     * @param integer $userCount
     * @return Company
     */
    public function setUserCount($userCount)
    {
        $this->userCount = $userCount;

        return $this;
    }

    /**
     * Get userCount
     *
     * @return integer 
     */
    public function getUserCount()
    {
        return $this->userCount;
    }
    /**
     * @var string
     */
    private $detailArea;


    /**
     * Set detailArea
     *
     * @param string $detailArea
     * @return Company
     */
    public function setDetailArea($detailArea)
    {
        $this->detailArea = $detailArea;

        return $this;
    }

    /**
     * Get detailArea
     *
     * @return string 
     */
    public function getDetailArea()
    {
        return $this->detailArea;
    }
    /**
     * @var integer
     */
    private $maxAcorn;


    /**
     * Set maxAcorn
     *
     * @param integer $maxAcorn
     * @return Company
     */
    public function setMaxAcorn($maxAcorn)
    {
        $this->maxAcorn = $maxAcorn;

        return $this;
    }

    /**
     * Get maxAcorn
     *
     * @return integer 
     */
    public function getMaxAcorn()
    {
        return $this->maxAcorn;
    }
    /**
     * @var string
     */
    private $amount;


    /**
     * Set amount
     *
     * @param string $amount
     * @return Company
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }
    /**
     * @var string
     */
    private $platform;


    /**
     * Set platform
     *
     * @param string $platform
     * @return Company
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return string 
     */
    public function getPlatform()
    {
        return $this->platform;
    }
    /**
     * @var integer
     */
    private $usingCount;


    /**
     * Set usingCount
     *
     * @param integer $usingCount
     * @return Company
     */
    public function setUsingCount($usingCount)
    {
        $this->usingCount = $usingCount;

        return $this;
    }

    /**
     * Get usingCount
     *
     * @return integer 
     */
    public function getUsingCount()
    {
        return $this->usingCount;
    }
}
