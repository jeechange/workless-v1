<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Industry
 */
class Industry
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $parentid;

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
    private $sort;


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
     * Set parentid
     *
     * @param integer $parentid
     * @return Industry
     */
    public function setParentid($parentid)
    {
        $this->parentid = $parentid;

        return $this;
    }

    /**
     * Get parentid
     *
     * @return integer 
     */
    public function getParentid()
    {
        return $this->parentid;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return Industry
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
     * @return Industry
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
     * Set sort
     *
     * @param integer $sort
     * @return Industry
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
}
