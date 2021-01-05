<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WelfareSettings
 */
class WelfareSettings
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
    private $materials;

    /**
     * @var integer
     */
    private $lucky;

    /**
     * @var string
     */
    private $luckyPrize;

    /**
     * @var integer
     */
    private $bonus;

    /**
     * @var string
     */
    private $bonusPool;

    /**
     * @var integer
     */
    private $snack;

    /**
     * @var integer
     */
    private $snackUserId;


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
     * @return WelfareSettings
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
     * Set materials
     *
     * @param integer $materials
     * @return WelfareSettings
     */
    public function setMaterials($materials)
    {
        $this->materials = $materials;

        return $this;
    }

    /**
     * Get materials
     *
     * @return integer 
     */
    public function getMaterials()
    {
        return $this->materials;
    }

    /**
     * Set lucky
     *
     * @param integer $lucky
     * @return WelfareSettings
     */
    public function setLucky($lucky)
    {
        $this->lucky = $lucky;

        return $this;
    }

    /**
     * Get lucky
     *
     * @return integer 
     */
    public function getLucky()
    {
        return $this->lucky;
    }

    /**
     * Set luckyPrize
     *
     * @param string $luckyPrize
     * @return WelfareSettings
     */
    public function setLuckyPrize($luckyPrize)
    {
        $this->luckyPrize = $luckyPrize;

        return $this;
    }

    /**
     * Get luckyPrize
     *
     * @return string 
     */
    public function getLuckyPrize()
    {
        return $this->luckyPrize;
    }

    /**
     * Set bonus
     *
     * @param integer $bonus
     * @return WelfareSettings
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * Get bonus
     *
     * @return integer 
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * Set bonusPool
     *
     * @param string $bonusPool
     * @return WelfareSettings
     */
    public function setBonusPool($bonusPool)
    {
        $this->bonusPool = $bonusPool;

        return $this;
    }

    /**
     * Get bonusPool
     *
     * @return string 
     */
    public function getBonusPool()
    {
        return $this->bonusPool;
    }

    /**
     * Set snack
     *
     * @param integer $snack
     * @return WelfareSettings
     */
    public function setSnack($snack)
    {
        $this->snack = $snack;

        return $this;
    }

    /**
     * Get snack
     *
     * @return integer 
     */
    public function getSnack()
    {
        return $this->snack;
    }

    /**
     * Set snackUserId
     *
     * @param integer $snackUserId
     * @return WelfareSettings
     */
    public function setSnackUserId($snackUserId)
    {
        $this->snackUserId = $snackUserId;

        return $this;
    }

    /**
     * Get snackUserId
     *
     * @return integer 
     */
    public function getSnackUserId()
    {
        return $this->snackUserId;
    }
    /**
     * @var integer
     */
    private $snackNum;


    /**
     * Set snackNum
     *
     * @param integer $snackNum
     * @return WelfareSettings
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
    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var string
     */
    private $memo;

    /**
     * @var string
     */
    private $sysmemo;


    /**
     * Set name
     *
     * @param string $name
     * @return WelfareSettings
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
     * Set types
     *
     * @param integer $types
     * @return WelfareSettings
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
     * Set memo
     *
     * @param string $memo
     * @return WelfareSettings
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
     * @return WelfareSettings
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
}
