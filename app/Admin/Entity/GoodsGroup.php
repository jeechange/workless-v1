<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsGroup
 */
class GoodsGroup
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
    private $skuTitle;

    /**
     * @var string
     */
    private $skuInfo;

    /**
     * @var string
     */
    private $pics;


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
     * @return GoodsGroup
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
     * Set skuTitle
     *
     * @param string $skuTitle
     * @return GoodsGroup
     */
    public function setSkuTitle($skuTitle)
    {
        $this->skuTitle = $skuTitle;

        return $this;
    }

    /**
     * Get skuTitle
     *
     * @return string 
     */
    public function getSkuTitle()
    {
        return $this->skuTitle;
    }

    /**
     * Set skuInfo
     *
     * @param string $skuInfo
     * @return GoodsGroup
     */
    public function setSkuInfo($skuInfo)
    {
        $this->skuInfo = $skuInfo;

        return $this;
    }

    /**
     * Get skuInfo
     *
     * @return string 
     */
    public function getSkuInfo()
    {
        return $this->skuInfo;
    }

    /**
     * Set pics
     *
     * @param string $pics
     * @return GoodsGroup
     */
    public function setPics($pics)
    {
        $this->pics = $pics;

        return $this;
    }

    /**
     * Get pics
     *
     * @return string 
     */
    public function getPics()
    {
        return $this->pics;
    }
}
