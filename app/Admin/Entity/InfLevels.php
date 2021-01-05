<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InfLevels
 */
class InfLevels
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
     * @var integer
     */
    private $levels;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $className;

    /**
     * @var integer
     */
    private $acorn;

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
     * @return InfLevels
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
     * Set levels
     *
     * @param integer $levels
     * @return InfLevels
     */
    public function setLevels($levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Get levels
     *
     * @return integer 
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return InfLevels
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
     * Set className
     *
     * @param string $className
     * @return InfLevels
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get className
     *
     * @return string 
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set acorn
     *
     * @param integer $acorn
     * @return InfLevels
     */
    public function setAcorn($acorn)
    {
        $this->acorn = $acorn;

        return $this;
    }

    /**
     * Get acorn
     *
     * @return integer 
     */
    public function getAcorn()
    {
        return $this->acorn;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return InfLevels
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
     * @var integer
     */
    private $sid;


    /**
     * Set sid
     *
     * @param integer $sid
     * @return InfLevels
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
     * @var string
     */
    private $classNames;


    /**
     * Set classNames
     *
     * @param string $classNames
     * @return InfLevels
     */
    public function setClassNames($classNames)
    {
        $this->classNames = $classNames;

        return $this;
    }

    /**
     * Get classNames
     *
     * @return string 
     */
    public function getClassNames()
    {
        return $this->classNames;
    }
}
