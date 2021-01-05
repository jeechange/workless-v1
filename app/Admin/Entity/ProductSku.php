<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductSku
 */
class ProductSku
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $productId;

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
    private $skuCode;

    /**
     * @var string
     */
    private $barCode;

    /**
     * @var integer
     */
    private $num;

    /**
     * @var integer
     */
    private $warning;

    /**
     * @var string
     */
    private $thumbs;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $skuStatus;


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
     * Set productId
     *
     * @param integer $productId
     * @return ProductSku
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set spec1
     *
     * @param string $spec1
     * @return ProductSku
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
     * @return ProductSku
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
     * @return ProductSku
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
     * @return ProductSku
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
     * @return ProductSku
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
     * Set skuCode
     *
     * @param string $skuCode
     * @return ProductSku
     */
    public function setSkuCode($skuCode)
    {
        $this->skuCode = $skuCode;

        return $this;
    }

    /**
     * Get skuCode
     *
     * @return string 
     */
    public function getSkuCode()
    {
        return $this->skuCode;
    }

    /**
     * Set barCode
     *
     * @param string $barCode
     * @return ProductSku
     */
    public function setBarCode($barCode)
    {
        $this->barCode = $barCode;

        return $this;
    }

    /**
     * Get barCode
     *
     * @return string 
     */
    public function getBarCode()
    {
        return $this->barCode;
    }

    /**
     * Set num
     *
     * @param integer $num
     * @return ProductSku
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
     * Set warning
     *
     * @param integer $warning
     * @return ProductSku
     */
    public function setWarning($warning)
    {
        $this->warning = $warning;

        return $this;
    }

    /**
     * Get warning
     *
     * @return integer 
     */
    public function getWarning()
    {
        return $this->warning;
    }

    /**
     * Set thumbs
     *
     * @param string $thumbs
     * @return ProductSku
     */
    public function setThumbs($thumbs)
    {
        $this->thumbs = $thumbs;

        return $this;
    }

    /**
     * Get thumbs
     *
     * @return string 
     */
    public function getThumbs()
    {
        return $this->thumbs;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return ProductSku
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
     * Set skuStatus
     *
     * @param integer $skuStatus
     * @return ProductSku
     */
    public function setSkuStatus($skuStatus)
    {
        $this->skuStatus = $skuStatus;

        return $this;
    }

    /**
     * Get skuStatus
     *
     * @return integer 
     */
    public function getSkuStatus()
    {
        return $this->skuStatus;
    }
}
