<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RbacAccredit
 */
class RbacAccredit
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $roleId;

    /**
     * @var integer
     */
    private $nodeId;

    /**
     * @var integer
     */
    private $enable;

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
     * Set roleId
     *
     * @param integer $roleId
     * @return RbacAccredit
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * Get roleId
     *
     * @return integer 
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set nodeId
     *
     * @param integer $nodeId
     * @return RbacAccredit
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;

        return $this;
    }

    /**
     * Get nodeId
     *
     * @return integer 
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * Set enable
     *
     * @param integer $enable
     * @return RbacAccredit
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;

        return $this;
    }

    /**
     * Get enable
     *
     * @return integer 
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return RbacAccredit
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
}
