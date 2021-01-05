<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyStock
 */
class CompanyStock {
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
    private $bonus;

    /**
     * @var string
     */
    private $releaseBonus;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $totalMoney;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set sid
     *
     * @param integer $sid
     * @return CompanyStock
     */
    public function setSid($sid) {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid
     *
     * @return integer
     */
    public function getSid() {
        return $this->sid;
    }

    /**
     * Set bonus
     *
     * @param string $bonus
     * @return CompanyStock
     */
    public function setBonus($bonus) {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * Get bonus
     *
     * @return string
     */
    public function getBonus() {
        return $this->bonus;
    }

    /**
     * Set release
     *
     * @param string $releaseBonus
     * @return CompanyStock
     */
    public function setReleaseBonus($releaseBonus) {
        $this->releaseBonus = $releaseBonus;

        return $this;
    }

    /**
     * Get release
     *
     * @return string
     */
    public function getReleaseBonus() {
        return $this->releaseBonus;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return CompanyStock
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Set totalMoney
     *
     * @param string $totalMoney
     * @return CompanyStock
     */
    public function setTotalMoney($totalMoney) {
        $this->totalMoney = $totalMoney;

        return $this;
    }

    /**
     * Get totalMoney
     *
     * @return string
     */
    public function getTotalMoney() {
        return $this->totalMoney;
    }
    /**
     * @var string
     */
    private $lockTime;


    /**
     * Set lockTime
     *
     * @param string $lockTime
     * @return CompanyStock
     */
    public function setLockTime($lockTime)
    {
        $this->lockTime = $lockTime;

        return $this;
    }

    /**
     * Get lockTime
     *
     * @return string 
     */
    public function getLockTime()
    {
        return $this->lockTime;
    }
}
