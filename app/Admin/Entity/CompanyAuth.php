<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyAuth
 */
class CompanyAuth
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
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $industry;

    /**
     * @var integer
     */
    private $scales;

    /**
     * @var string
     */
    private $licenseCode;

    /**
     * @var string
     */
    private $licensePic;

    /**
     * @var string
     */
    private $licenseRegisterMoney;

    /**
     * @var \DateTime
     */
    private $licenseRegisterDate;

    /**
     * @var \DateTime
     */
    private $licenseRegisterExpiry;

    /**
     * @var string
     */
    private $legal;

    /**
     * @var string
     */
    private $legalIdentity;

    /**
     * @var string
     */
    private $legalIdentityPic;

    /**
     * @var string
     */
    private $legalIdentityBack;

    /**
     * @var string
     */
    private $contact;

    /**
     * @var string
     */
    private $contactPhone;

    /**
     * @var string
     */
    private $email;

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
     * @var string
     */
    private $address;

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
     * @return CompanyAuth
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
     * @return CompanyAuth
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
     * Set names
     *
     * @param string $names
     * @return CompanyAuth
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
     * Set industry
     *
     * @param string $industry
     * @return CompanyAuth
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;

        return $this;
    }

    /**
     * Get industry
     *
     * @return string 
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * Set scales
     *
     * @param integer $scales
     * @return CompanyAuth
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
     * Set licenseCode
     *
     * @param string $licenseCode
     * @return CompanyAuth
     */
    public function setLicenseCode($licenseCode)
    {
        $this->licenseCode = $licenseCode;

        return $this;
    }

    /**
     * Get licenseCode
     *
     * @return string 
     */
    public function getLicenseCode()
    {
        return $this->licenseCode;
    }

    /**
     * Set licensePic
     *
     * @param string $licensePic
     * @return CompanyAuth
     */
    public function setLicensePic($licensePic)
    {
        $this->licensePic = $licensePic;

        return $this;
    }

    /**
     * Get licensePic
     *
     * @return string 
     */
    public function getLicensePic()
    {
        return $this->licensePic;
    }

    /**
     * Set licenseRegisterMoney
     *
     * @param string $licenseRegisterMoney
     * @return CompanyAuth
     */
    public function setLicenseRegisterMoney($licenseRegisterMoney)
    {
        $this->licenseRegisterMoney = $licenseRegisterMoney;

        return $this;
    }

    /**
     * Get licenseRegisterMoney
     *
     * @return string 
     */
    public function getLicenseRegisterMoney()
    {
        return $this->licenseRegisterMoney;
    }

    /**
     * Set licenseRegisterDate
     *
     * @param \DateTime $licenseRegisterDate
     * @return CompanyAuth
     */
    public function setLicenseRegisterDate($licenseRegisterDate)
    {
        $this->licenseRegisterDate = $licenseRegisterDate;

        return $this;
    }

    /**
     * Get licenseRegisterDate
     *
     * @return \DateTime 
     */
    public function getLicenseRegisterDate()
    {
        return $this->licenseRegisterDate;
    }

    /**
     * Set licenseRegisterExpiry
     *
     * @param \DateTime $licenseRegisterExpiry
     * @return CompanyAuth
     */
    public function setLicenseRegisterExpiry($licenseRegisterExpiry)
    {
        $this->licenseRegisterExpiry = $licenseRegisterExpiry;

        return $this;
    }

    /**
     * Get licenseRegisterExpiry
     *
     * @return \DateTime 
     */
    public function getLicenseRegisterExpiry()
    {
        return $this->licenseRegisterExpiry;
    }

    /**
     * Set legal
     *
     * @param string $legal
     * @return CompanyAuth
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
     * Set legalIdentity
     *
     * @param string $legalIdentity
     * @return CompanyAuth
     */
    public function setLegalIdentity($legalIdentity)
    {
        $this->legalIdentity = $legalIdentity;

        return $this;
    }

    /**
     * Get legalIdentity
     *
     * @return string 
     */
    public function getLegalIdentity()
    {
        return $this->legalIdentity;
    }

    /**
     * Set legalIdentityPic
     *
     * @param string $legalIdentityPic
     * @return CompanyAuth
     */
    public function setLegalIdentityPic($legalIdentityPic)
    {
        $this->legalIdentityPic = $legalIdentityPic;

        return $this;
    }

    /**
     * Get legalIdentityPic
     *
     * @return string 
     */
    public function getLegalIdentityPic()
    {
        return $this->legalIdentityPic;
    }

    /**
     * Set legalIdentityBack
     *
     * @param string $legalIdentityBack
     * @return CompanyAuth
     */
    public function setLegalIdentityBack($legalIdentityBack)
    {
        $this->legalIdentityBack = $legalIdentityBack;

        return $this;
    }

    /**
     * Get legalIdentityBack
     *
     * @return string 
     */
    public function getLegalIdentityBack()
    {
        return $this->legalIdentityBack;
    }

    /**
     * Set contact
     *
     * @param string $contact
     * @return CompanyAuth
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contactPhone
     *
     * @param string $contactPhone
     * @return CompanyAuth
     */
    public function setContactPhone($contactPhone)
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    /**
     * Get contactPhone
     *
     * @return string 
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return CompanyAuth
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
     * Set province
     *
     * @param string $province
     * @return CompanyAuth
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
     * @return CompanyAuth
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
     * @return CompanyAuth
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
     * Set address
     *
     * @param string $address
     * @return CompanyAuth
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
     * Set status
     *
     * @param integer $status
     * @return CompanyAuth
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
