<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 */
class Service
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
    private $sCode;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var string
     */
    private $spec;

    /**
     * @var string
     */
    private $money;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $putawayTime;

    /**
     * @var string
     */
    private $content;

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
     * Set names
     *
     * @param string $names
     * @return Service
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
     * Set sCode
     *
     * @param string $sCode
     * @return Service
     */
    public function setSCode($sCode)
    {
        $this->sCode = $sCode;

        return $this;
    }

    /**
     * Get sCode
     *
     * @return string 
     */
    public function getSCode()
    {
        return $this->sCode;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return Service
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
     * Set spec
     *
     * @param string $spec
     * @return Service
     */
    public function setSpec($spec)
    {
        $this->spec = $spec;

        return $this;
    }

    /**
     * Get spec
     *
     * @return string 
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * Set money
     *
     * @param string $money
     * @return Service
     */
    public function setMoney($money)
    {
        $this->money = $money;

        return $this;
    }

    /**
     * Get money
     *
     * @return string 
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Service
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
     * Set putawayTime
     *
     * @param \DateTime $putawayTime
     * @return Service
     */
    public function setPutawayTime($putawayTime)
    {
        $this->putawayTime = $putawayTime;

        return $this;
    }

    /**
     * Get putawayTime
     *
     * @return \DateTime 
     */
    public function getPutawayTime()
    {
        return $this->putawayTime;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Service
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
     * @return Service
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
