<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MoneyLaunch
 */
class MoneyLaunch
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $memo;

    /**
     * @var string
     */
    private $sysmemo;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var integer
     */
    private $num;

    /**
     * @var string
     */
    private $joinPerson;

    /**
     * @var string
     */
    private $examinePerson;

    /**
     * @var \DateTime
     */
    private $endTime;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var string
     */
    private $source;

    /**
     * @var integer
     */
    private $sid;


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
     * Set name
     *
     * @param string $name
     * @return MoneyLaunch
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return MoneyLaunch
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
     * Set sysmemo
     *
     * @param string $sysmemo
     * @return MoneyLaunch
     */
    public function setSysmemo($sysmemo)
    {
        $this->sysmemo = $sysmemo;

        return $this;
    }

    /**
     * Get sysmemo
     *
     * @return string 
     */
    public function getSysmemo()
    {
        return $this->sysmemo;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return MoneyLaunch
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return MoneyLaunch
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
     * Set num
     *
     * @param integer $num
     * @return MoneyLaunch
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer 
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set joinPerson
     *
     * @param string $joinPerson
     * @return MoneyLaunch
     */
    public function setJoinPerson($joinPerson)
    {
        $this->joinPerson = $joinPerson;

        return $this;
    }

    /**
     * Get joinPerson
     *
     * @return string 
     */
    public function getJoinPerson()
    {
        return $this->joinPerson;
    }

    /**
     * Set examinePerson
     *
     * @param string $examinePerson
     * @return MoneyLaunch
     */
    public function setExaminePerson($examinePerson)
    {
        $this->examinePerson = $examinePerson;

        return $this;
    }

    /**
     * Get examinePerson
     *
     * @return string 
     */
    public function getExaminePerson()
    {
        return $this->examinePerson;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return MoneyLaunch
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return MoneyLaunch
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
     * Set source
     *
     * @param string $source
     * @return MoneyLaunch
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set sid
     *
     * @param integer $sid
     * @return MoneyLaunch
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
     * @var integer
     */
    private $categoryId;


    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return MoneyLaunch
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
    /**
     * @var string
     */
    private $sortToken;

    /**
     * @var string
     */
    private $sortInfo;


    /**
     * Set sortToken
     *
     * @param string $sortToken
     * @return MoneyLaunch
     */
    public function setSortToken($sortToken)
    {
        $this->sortToken = $sortToken;

        return $this;
    }

    /**
     * Get sortToken
     *
     * @return string 
     */
    public function getSortToken()
    {
        return $this->sortToken;
    }

    /**
     * Set sortInfo
     *
     * @param string $sortInfo
     * @return MoneyLaunch
     */
    public function setSortInfo($sortInfo)
    {
        $this->sortInfo = $sortInfo;

        return $this;
    }

    /**
     * Get sortInfo
     *
     * @return string 
     */
    public function getSortInfo()
    {
        return $this->sortInfo;
    }
}
