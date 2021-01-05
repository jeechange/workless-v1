<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WelfareSnack
 */
class WelfareSnack
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
    private $icon;

    /**
     * @var string
     */
    private $acorn;

    /**
     * @var string
     */
    private $num;

    /**
     * @var string
     */
    private $unit;

    /**
     * @var string
     */
    private $content;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var \DateTime
     */
    private $addTime;

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
     * Set sid
     *
     * @param integer $sid
     * @return WelfareSnack
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
     * @return WelfareSnack
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
     * @return WelfareSnack
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
     * Set icon
     *
     * @param string $icon
     * @return WelfareSnack
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set acorn
     *
     * @param string $acorn
     * @return WelfareSnack
     */
    public function setAcorn($acorn)
    {
        $this->acorn = $acorn;

        return $this;
    }

    /**
     * Get acorn
     *
     * @return string 
     */
    public function getAcorn()
    {
        return $this->acorn;
    }

    /**
     * Set num
     *
     * @param string $num
     * @return WelfareSnack
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return string 
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set unit
     *
     * @param string $unit
     * @return WelfareSnack
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string 
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return WelfareSnack
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return WelfareSnack
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
     * @return WelfareSnack
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return WelfareSnack
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
     * @return WelfareSnack
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
     * @var string
     */
    private $everyNum;


    /**
     * Set everyNum
     *
     * @param string $everyNum
     * @return WelfareSnack
     */
    public function setEveryNum($everyNum)
    {
        $this->everyNum = $everyNum;

        return $this;
    }

    /**
     * Get everyNum
     *
     * @return string 
     */
    public function getEveryNum()
    {
        return $this->everyNum;
    }
}
