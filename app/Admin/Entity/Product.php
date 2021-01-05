<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 */
class Product
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
     * @var string
     */
    private $code;

    /**
     * @var integer
     */
    private $categoryId;

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
     * @var string
     */
    private $unit;

    /**
     * @var string
     */
    private $keywords;

    /**
     * @var string
     */
    private $price;

    /**
     * @var integer
     */
    private $postageFree;

    /**
     * @var integer
     */
    private $putaway;

    /**
     * @var integer
     */
    private $weight;

    /**
     * @var string
     */
    private $description;

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
    private $sort;

    /**
     * @var string
     */
    private $tabloid;

    /**
     * @var integer
     */
    private $sId;


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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set specId
     *
     * @param integer $specId
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set unit
     *
     * @param string $unit
     * @return Product
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
     * Set keywords
     *
     * @param string $keywords
     * @return Product
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
     * Set price
     *
     * @param string $price
     * @return Product
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
     * Set postageFree
     *
     * @param integer $postageFree
     * @return Product
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
     * Set putaway
     *
     * @param integer $putaway
     * @return Product
     */
    public function setPutaway($putaway)
    {
        $this->putaway = $putaway;

        return $this;
    }

    /**
     * Get putaway
     *
     * @return integer 
     */
    public function getPutaway()
    {
        return $this->putaway;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Product
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
     * Set description
     *
     * @param string $description
     * @return Product
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set sort
     *
     * @param integer $sort
     * @return Product
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
     * Set tabloid
     *
     * @param string $tabloid
     * @return Product
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
     * Set sId
     *
     * @param integer $sId
     * @return Product
     */
    public function setSId($sId)
    {
        $this->sId = $sId;

        return $this;
    }

    /**
     * Get sId
     *
     * @return integer 
     */
    public function getSId()
    {
        return $this->sId;
    }
    /**
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return Product
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
