<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetSetting
 */
class TargetSetting
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $principle;

    /**
     * @var string
     */
    private $specs;


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
     * Set principle
     *
     * @param string $principle
     * @return TargetSetting
     */
    public function setPrinciple($principle)
    {
        $this->principle = $principle;

        return $this;
    }

    /**
     * Get principle
     *
     * @return string 
     */
    public function getPrinciple()
    {
        return $this->principle;
    }

    /**
     * Set specs
     *
     * @param string $specs
     * @return TargetSetting
     */
    public function setSpecs($specs)
    {
        $this->specs = $specs;

        return $this;
    }

    /**
     * Get specs
     *
     * @return string 
     */
    public function getSpecs()
    {
        return $this->specs;
    }
}
