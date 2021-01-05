<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Goods
 */
class Goods
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
    private $names;

    /**
     * @var string
     */
    private $code;

    /**
     * @var integer
     */
    private $categoryId;

    /**
     * @var string
     */
    private $unit;

    /**
     * @var integer
     */
    private $payTypes;

    /**
     * @var integer
     */
    private $consume;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $tabloid;

    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $sort;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var string
     */
    private $platform;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $sales;

    /**
     * @var \DateTime
     */
    private $startTime;

    /**
     * @var integer
     */
    private $specId;

    /**
     * @var string
     */
    private $spec1;

    /**
     * @var string
     */
    private $spec2;

    /**
     * @var string
     */
    private $spec3;

    /**
     * @var string
     */
    private $spec4;

    /**
     * @var string
     */
    private $spec5;

    /**
     * @var integer
     */
    private $postageFree;

    /**
     * @var integer
     */
    private $weight;


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
     * @return Goods
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
     * Set names
     *
     * @param string $names
     * @return Goods
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
     * Set code
     *
     * @param string $code
     * @return Goods
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return Goods
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
     * Set unit
     *
     * @param string $unit
     * @return Goods
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
     * Set payTypes
     *
     * @param integer $payTypes
     * @return Goods
     */
    public function setPayTypes($payTypes)
    {
        $this->payTypes = $payTypes;

        return $this;
    }

    /**
     * Get payTypes
     *
     * @return integer 
     */
    public function getPayTypes()
    {
        return $this->payTypes;
    }

    /**
     * Set consume
     *
     * @param integer $consume
     * @return Goods
     */
    public function setConsume($consume)
    {
        $this->consume = $consume;

        return $this;
    }

    /**
     * Get consume
     *
     * @return integer 
     */
    public function getConsume()
    {
        return $this->consume;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Goods
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set tabloid
     *
     * @param string $tabloid
     * @return Goods
     */
    public function setTabloid($tabloid)
    {
        $this->tabloid = $tabloid;

        return $this;
    }

    /**
     * Get tabloid
     *
     * @return string 
     */
    public function getTabloid()
    {
        return $this->tabloid;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Goods
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return Goods
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Goods
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
     * Set updateTime
     *
     * @param \DateTime $updateTime
     * @return Goods
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;

        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime 
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set platform
     *
     * @param string $platform
     * @return Goods
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
     * Set status
     *
     * @param integer $status
     * @return Goods
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
     * Set sales
     *
     * @param integer $sales
     * @return Goods
     */
    public function setSales($sales)
    {
        $this->sales = $sales;

        return $this;
    }

    /**
     * Get sales
     *
     * @return integer 
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return Goods
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set specId
     *
     * @param integer $specId
     * @return Goods
     */
    public function setSpecId($specId)
    {
        $this->specId = $specId;

        return $this;
    }

    /**
     * Get specId
     *
     * @return integer 
     */
    public function getSpecId()
    {
        return $this->specId;
    }

    /**
     * Set spec1
     *
     * @param string $spec1
     * @return Goods
     */
    public function setSpec1($spec1)
    {
        $this->spec1 = $spec1;

        return $this;
    }

    /**
     * Get spec1
     *
     * @return string 
     */
    public function getSpec1()
    {
        return $this->spec1;
    }

    /**
     * Set spec2
     *
     * @param string $spec2
     * @return Goods
     */
    public function setSpec2($spec2)
    {
        $this->spec2 = $spec2;

        return $this;
    }

    /**
     * Get spec2
     *
     * @return string 
     */
    public function getSpec2()
    {
        return $this->spec2;
    }

    /**
     * Set spec3
     *
     * @param string $spec3
     * @return Goods
     */
    public function setSpec3($spec3)
    {
        $this->spec3 = $spec3;

        return $this;
    }

    /**
     * Get spec3
     *
     * @return string 
     */
    public function getSpec3()
    {
        return $this->spec3;
    }

    /**
     * Set spec4
     *
     * @param string $spec4
     * @return Goods
     */
    public function setSpec4($spec4)
    {
        $this->spec4 = $spec4;

        return $this;
    }

    /**
     * Get spec4
     *
     * @return string 
     */
    public function getSpec4()
    {
        return $this->spec4;
    }

    /**
     * Set spec5
     *
     * @param string $spec5
     * @return Goods
     */
    public function setSpec5($spec5)
    {
        $this->spec5 = $spec5;

        return $this;
    }

    /**
     * Get spec5
     *
     * @return string 
     */
    public function getSpec5()
    {
        return $this->spec5;
    }

    /**
     * Set postageFree
     *
     * @param integer $postageFree
     * @return Goods
     */
    public function setPostageFree($postageFree)
    {
        $this->postageFree = $postageFree;

        return $this;
    }

    /**
     * Get postageFree
     *
     * @return integer 
     */
    public function getPostageFree()
    {
        return $this->postageFree;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Goods
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }
    /**
     * @var integer
     */
    private $gid;

    /**
     * @var string
     */
    private $codeBar;

    /**
     * @var string
     */
    private $skuTitle;

    /**
     * @var string
     */
    private $skuInfo;

    /**
     * @var float
     */
    private $num;

    /**
     * @var string
     */
    private $priceMarket;

    /**
     * @var string
     */
    private $pricePv;

    /**
     * @var string
     */
    private $priceRatio;

    /**
     * @var string
     */
    private $picCover;

    /**
     * @var string
     */
    private $pics;

    /**
     * @var string
     */
    private $video;

    /**
     * @var string
     */
    private $descriptionM;

    /**
     * @var integer
     */
    private $inventory;

    /**
     * @var integer
     */
    private $inventoryWarning;

    /**
     * @var integer
     */
    private $priceInfluence;

    /**
     * @var string
     */
    private $qgpMemo;


    /**
     * Set gid
     *
     * @param integer $gid
     * @return Goods
     */
    public function setGid($gid)
    {
        $this->gid = $gid;

        return $this;
    }

    /**
     * Get gid
     *
     * @return integer 
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * Set codeBar
     *
     * @param string $codeBar
     * @return Goods
     */
    public function setCodeBar($codeBar)
    {
        $this->codeBar = $codeBar;

        return $this;
    }

    /**
     * Get codeBar
     *
     * @return string 
     */
    public function getCodeBar()
    {
        return $this->codeBar;
    }

    /**
     * Set skuTitle
     *
     * @param string $skuTitle
     * @return Goods
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
     * @return Goods
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
     * Set num
     *
     * @param float $num
     * @return Goods
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return float 
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set priceMarket
     *
     * @param string $priceMarket
     * @return Goods
     */
    public function setPriceMarket($priceMarket)
    {
        $this->priceMarket = $priceMarket;

        return $this;
    }

    /**
     * Get priceMarket
     *
     * @return string 
     */
    public function getPriceMarket()
    {
        return $this->priceMarket;
    }

    /**
     * Set pricePv
     *
     * @param string $pricePv
     * @return Goods
     */
    public function setPricePv($pricePv)
    {
        $this->pricePv = $pricePv;

        return $this;
    }

    /**
     * Get pricePv
     *
     * @return string 
     */
    public function getPricePv()
    {
        return $this->pricePv;
    }

    /**
     * Set priceRatio
     *
     * @param string $priceRatio
     * @return Goods
     */
    public function setPriceRatio($priceRatio)
    {
        $this->priceRatio = $priceRatio;

        return $this;
    }

    /**
     * Get priceRatio
     *
     * @return string 
     */
    public function getPriceRatio()
    {
        return $this->priceRatio;
    }

    /**
     * Set picCover
     *
     * @param string $picCover
     * @return Goods
     */
    public function setPicCover($picCover)
    {
        $this->picCover = $picCover;

        return $this;
    }

    /**
     * Get picCover
     *
     * @return string 
     */
    public function getPicCover()
    {
        return $this->picCover;
    }

    /**
     * Set pics
     *
     * @param string $pics
     * @return Goods
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

    /**
     * Set video
     *
     * @param string $video
     * @return Goods
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return string 
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set descriptionM
     *
     * @param string $descriptionM
     * @return Goods
     */
    public function setDescriptionM($descriptionM)
    {
        $this->descriptionM = $descriptionM;

        return $this;
    }

    /**
     * Get descriptionM
     *
     * @return string 
     */
    public function getDescriptionM()
    {
        return $this->descriptionM;
    }

    /**
     * Set inventory
     *
     * @param integer $inventory
     * @return Goods
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get inventory
     *
     * @return integer 
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * Set inventoryWarning
     *
     * @param integer $inventoryWarning
     * @return Goods
     */
    public function setInventoryWarning($inventoryWarning)
    {
        $this->inventoryWarning = $inventoryWarning;

        return $this;
    }

    /**
     * Get inventoryWarning
     *
     * @return integer 
     */
    public function getInventoryWarning()
    {
        return $this->inventoryWarning;
    }

    /**
     * Set priceInfluence
     *
     * @param integer $priceInfluence
     * @return Goods
     */
    public function setPriceInfluence($priceInfluence)
    {
        $this->priceInfluence = $priceInfluence;

        return $this;
    }

    /**
     * Get priceInfluence
     *
     * @return integer 
     */
    public function getPriceInfluence()
    {
        return $this->priceInfluence;
    }

    /**
     * Set qgpMemo
     *
     * @param string $qgpMemo
     * @return Goods
     */
    public function setQgpMemo($qgpMemo)
    {
        $this->qgpMemo = $qgpMemo;

        return $this;
    }

    /**
     * Get qgpMemo
     *
     * @return string 
     */
    public function getQgpMemo()
    {
        return $this->qgpMemo;
    }
}
